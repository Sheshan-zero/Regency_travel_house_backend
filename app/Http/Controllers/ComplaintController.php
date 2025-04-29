<?php

namespace App\Http\Controllers;

use App\Mail\ContactUsMail;
use App\Models\Complaint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class ComplaintController extends Controller
{
    /**
     * Display a listing of all complaints (admin/staff view).
     */
    public function index()
    {
        $complaints = Complaint::with(['customer', 'booking', 'staff'])->latest()->get();
        return response()->json($complaints);
    }

    /**
     * Store a newly created complaint from a customer.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'booking_id' => 'nullable|exists:bookings,id',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $complaint = Complaint::create([
            'customer_id' => Auth::id(),
            'booking_id' => $validated['booking_id'] ?? null,
            'subject' => $validated['subject'],
            'message' => $validated['message'],
            'status' => 'in progress',
            'submitted_at' => now(),
        ]);

        $user = Auth::user(); // Get the logged-in customer

        Mail::to('support@regencytravel.com')->send(
            new ContactUsMail(
                $user->full_name,
                $user->last_name,
                $user->email,
                $validated['subject'],
                $validated['message']
            )
        );

        return response()->json(['message' => 'Complaint submitted successfully', 'complaint' => $complaint], 201);
    }

    /**
     * Display a specific complaint.
     */
    public function show(Complaint $complaint)
    {
        $complaint->load(['customer', 'booking', 'staff']);
        return response()->json($complaint);
    }

    /**
     * Update a complaint (for marking resolved or assigning staff).
     */
    public function update(Request $request, Complaint $complaint)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,in_progress,resolved',
            'handled_by' => 'nullable|exists:staff,id',
            'resolved_at' => 'nullable|date',
        ]);

        $complaint->update([
            'status' => $validated['status'],
            'handled_by' => $validated['handled_by'] ?? $complaint->handled_by,
            'resolved_at' => $validated['status'] === 'resolved' ? now() : null,
        ]);

        return response()->json(['message' => 'Complaint updated successfully', 'complaint' => $complaint]);
    }

    /**
     * Delete a complaint.
     */
    public function destroy(Complaint $complaint)
    {
        $complaint->delete();
        return response()->json(['message' => 'Complaint deleted successfully']);
    }
}
