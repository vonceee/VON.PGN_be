<?php

namespace App\Traits;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

trait DetectsCountry
{
    /**
     * Detect country code from IP address.
     *
     * @param string $ip
     * @return string|null
     */
    protected function detectCountry(string $ip): ?string
    {
        try {
            Log::info("Attempting country detection for IP: {$ip}");

            // In development, handle local IP addresses
            if (($ip === '127.0.0.1' || $ip === '::1') && app()->environment(['local', 'testing'])) {
                Log::info("Local IP detected in dev environment, using fallback 8.8.8.8 (USA)");
                $ip = '8.8.8.8'; // Fallback for local testing
            }

            $response = Http::timeout(3)->get("http://ip-api.com/json/{$ip}");
            
            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['status']) && $data['status'] === 'success') {
                    $countryCode = $data['countryCode'] ?? null;
                    Log::info("Detected country code: {$countryCode} for IP: {$ip}");
                    return $countryCode;
                }
                Log::warning("IP-API returned unsuccessful status: " . ($data['message'] ?? 'Unknown error'));
            } else {
                Log::warning("IP-API request failed with status: " . $response->status());
            }
        } catch (\Exception $e) {
            Log::warning('Country detection failed: ' . $e->getMessage());
        }

        return null;
    }
}
