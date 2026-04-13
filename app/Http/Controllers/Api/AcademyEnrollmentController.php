<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AcademyEnrollment;
use App\Notifications\StudentEnrollmentConfirmation;
use App\Notifications\AdminNewEnrollmentNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;

class AcademyEnrollmentController extends Controller
{
    /**
     * Submit an enrollment for VonChess Academy
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'contact_number' => 'required|string|max:20',
            'chess_level' => 'required|string|max:50',
            'experience' => 'nullable|string|max:5000',
        ]);

        $enrollment = AcademyEnrollment::create($validated);

        try {
            // Notify Student
            $enrollment->notify(new StudentEnrollmentConfirmation($enrollment));

            // Notify Admin
            $adminEmail = env('ADMIN_NOTIFICATION_EMAIL', env('MAIL_FROM_ADDRESS'));
            Notification::route('mail', $adminEmail)
                ->notify(new AdminNewEnrollmentNotification($enrollment));
        } catch (\Exception $e) {
            // Log the error but don't fail the request
            Log::error('Academy Enrollment Notification Error: ' . $e->getMessage());
        }

        return response()->json([
            'message' => 'Enrollment submitted successfully.',
            'enrollment_id' => $enrollment->id
        ], 201);
    }
}
