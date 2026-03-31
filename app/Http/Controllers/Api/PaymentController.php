<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    private string $apiUrl = 'https://api.paymongo.com/v1';
    private string $amount = '9900'; // 9900 centavos = PHP 99.00

    private function getSecretKey(): string
    {
        return config('services.paymongo.secret_key');
    }

    private function getFrontendUrl(): string
    {
        return config('app.frontend_url', env('FRONTEND_URL', 'http://localhost:4200'));
    }

    /**
     * Create a PayMongo Checkout Session for verified organizer upgrade.
     */
    public function createCheckout(Request $request)
    {
        $user = $request->user();

        if ($user->verified_organizer) {
            return response()->json(['message' => 'You are already a verified organizer.'], 400);
        }

        $frontendUrl = $this->getFrontendUrl();

        $response = Http::withBasicAuth($this->getSecretKey(), '')
            ->post("{$this->apiUrl}/checkout_sessions", [
                'data' => [
                    'attributes' => [
                        'send_email_receipt' => true,
                        'show_description' => true,
                        'show_line_items' => true,
                        'line_items' => [
                            [
                                'currency' => 'PHP',
                                'amount' => (int) $this->amount,
                                'name' => 'Verified Organizer Subscription',
                                'description' => 'Monthly subscription for verified organizer badge and tournament priority listing.',
                                'quantity' => 1,
                            ],
                        ],
                        'payment_method_types' => [
                            'gcash',
                            'paymaya',
                            'grab_pay',
                            'shopeepay',
                            'card',
                            'dob',
                            'qrph',
                        ],
                        'reference_number' => 'verify_' . $user->id . '_' . time(),
                        'description' => 'VON.PGN Verified Organizer — Monthly Subscription',
                        'success_url' => $frontendUrl . '/profile?payment=success',
                        'cancel_url' => $frontendUrl . '/profile?payment=cancelled',
                        'metadata' => [
                            'user_id' => (string) $user->id,
                            'type' => 'verified_organizer',
                        ],
                    ],
                ],
            ]);

        if ($response->failed()) {
            Log::error('PayMongo checkout creation failed', $response->json());
            return response()->json(['message' => 'Failed to create payment session.'], 500);
        }

        $checkoutData = $response->json('data');

        Payment::create([
            'user_id' => $user->id,
            'paymongo_checkout_id' => $checkoutData['id'],
            'amount' => (int) $this->amount,
            'currency' => 'PHP',
            'description' => 'Verified Organizer Subscription',
            'status' => 'pending',
        ]);

        return response()->json([
            'checkout_url' => $checkoutData['attributes']['checkout_url'],
            'checkout_session_id' => $checkoutData['id'],
        ]);
    }

    /**
     * Handle PayMongo webhook events.
     */
    public function webhook(Request $request)
    {
        $payload = $request->all();
        $eventType = $payload['data']['attributes']['type'] ?? null;

        Log::info('PayMongo webhook received', ['event' => $eventType]);

        if ($eventType === 'checkout_session.payment.paid') {
            $this->handleSuccessfulPayment($payload['data']['attributes']['data']);
        }

        return response()->json(['received' => true]);
    }

    private function handleSuccessfulPayment(array $checkoutSession): void
    {
        $checkoutId = $checkoutSession['id'] ?? null;
        if (!$checkoutId) return;

        $payment = Payment::where('paymongo_checkout_id', $checkoutId)->first();
        if (!$payment) {
            Log::warning('Payment record not found for checkout', ['checkout_id' => $checkoutId]);
            return;
        }

        if ($payment->status === 'paid') return;

        $payments = $checkoutSession['attributes']['payments'] ?? [];
        $paymongoPaymentId = $payments[0]['id'] ?? null;
        $paymentMethod = $payments[0]['attributes']['source']['type'] ?? 'unknown';

        $payment->update([
            'status' => 'paid',
            'paymongo_payment_id' => $paymongoPaymentId,
            'payment_method' => $paymentMethod,
            'paid_at' => now(),
        ]);

        $user = $payment->user;
        if ($user && !$user->verified_organizer) {
            $user->update(['verified_organizer' => true]);
            Log::info('User verified as organizer', ['user_id' => $user->id]);
        }
    }

    /**
     * Return the current user's payment history.
     */
    public function history(Request $request)
    {
        $payments = Payment::where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(['data' => $payments]);
    }
}
