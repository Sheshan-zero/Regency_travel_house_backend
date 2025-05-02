<?php

namespace App\Http\Controllers;

use App\Models\JobApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use App\Mail\JobApplicationAdminNotification;
use App\Mail\JobApplicationThankYou;

class JobApplicationController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'nullable|string|max:20',
            'position_applied' => 'required|string|max:255',
            'cover_letter' => 'nullable|string',
            'cv' => 'required|mimes:pdf,doc,docx|max:2048'
        ]);

        // Store the uploaded CV file
        $path = $request->file('cv')->store('job_cvs', 'public');

        // Save to DB
        $application = JobApplication::create([
            'full_name' => $validated['full_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'position_applied' => $validated['position_applied'],
            'cover_letter' => $validated['cover_letter'] ?? null,
            'cv_path' => $path,
        ]);

        // Send email to admin
        Mail::to('sheshandealwis20021@gmail.com')->send(new JobApplicationAdminNotification($application));

        // Send thank-you email to applicant
        Mail::to($application->email)->send(new JobApplicationThankYou($application));

        return response()->json(['message' => 'Application submitted successfully.']);
    }
}
