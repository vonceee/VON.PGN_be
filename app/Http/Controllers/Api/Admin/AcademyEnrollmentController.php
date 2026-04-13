<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademyEnrollment;
use Illuminate\Http\Request;

class AcademyEnrollmentController extends Controller
{
    /**
     * List all enrollments
     */
    public function index(Request $request)
    {
        $query = AcademyEnrollment::query();

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('contact_number', 'like', "%{$search}%");
            });
        }

        return response()->json($query->orderBy('created_at', 'desc')->get());
    }

    /**
     * Show a single enrollment
     */
    public function show($id)
    {
        return response()->json(AcademyEnrollment::findOrFail($id));
    }

    /**
     * Update enrollment status
     */
    public function update(Request $request, $id)
    {
        $enrollment = AcademyEnrollment::findOrFail($id);
        
        $validated = $request->validate([
            'status' => 'required|in:pending,contacted,confirmed,paid,cancelled',
        ]);

        $enrollment->update($validated);

        return response()->json($enrollment);
    }

    /**
     * Delete an enrollment
     */
    public function destroy($id)
    {
        $enrollment = AcademyEnrollment::findOrFail($id);
        $enrollment->delete();

        return response()->json(['message' => 'Enrollment deleted successfully']);
    }
}
