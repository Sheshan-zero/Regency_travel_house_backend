<?php

namespace App\Http\Controllers;

use App\Models\Quote;
use App\Models\Destination;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Mail\QuoteResponseMail;
use Illuminate\Support\Facades\Mail;
use App\Models\Booking;


class AdminQuoteController extends Controller
{
    public function index()
    {
        $staff = Auth::guard('staff')->user();

        if (!in_array($staff->role, ['Admin', 'manager'])) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $quotes = Quote::with(['customer', 'destination', 'package', 'respondedBy'])->orderBy('created_at', 'desc')->get();
        return response()->json($quotes);
    }

    public function show($id)
    {
        $staff = Auth::guard('staff')->user();

        if (!in_array($staff->role, ['Admin', 'manager'])) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $quote = Quote::with(['customer', 'destination', 'respondedBy'])->find($id);

        return $quote
            ? response()->json($quote)
            : response()->json(['message' => 'Quote not found'], 404);
    }
    public function respond(Request $request, $id)
    {
        $staff = Auth::guard('staff')->user();

        if (!in_array($staff->role, ['Admin', 'manager'])) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'estimated_price' => 'required|numeric|min:0',
            'status' => 'required|in:responded,expired'
        ]);

        $quote = Quote::with('customer', 'package')->find($id);

        if (!$quote) {
            return response()->json(['message' => 'Quote not found'], 404);
        }

        if ($quote->status !== 'pending') {
            return response()->json(['message' => 'This quote has already been responded to.'], 409);
        }

        // Step 1: Update the quote
        $quote->update([
            'estimated_price' => $request->estimated_price,
            'status' => $request->status,
            'responded_by' => $staff->id
        ]);

        // Step 2: Create a pending booking linked to this quote
        Booking::create([
            'customer_id' => $quote->customer_id,
            'package_id' => $quote->package_id,
            'start_date' => $quote->start_date,
            'end_date' => $quote->end_date,
            'number_of_people' => $quote->number_of_people,
            'status' => 'pending',
            'quote_id' => $quote->id
        ]);

        // Step 3: Send email to customer
        Mail::to($quote->customer->email)->send(new QuoteResponseMail($quote));

        return response()->json([
            'message' => 'Quote responded. Booking created and email sent.',
            'quote' => $quote->load('customer', 'package', 'respondedBy')
        ]);
    }
}
