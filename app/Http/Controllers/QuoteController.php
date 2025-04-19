<?php

namespace App\Http\Controllers;

use App\Models\Quote;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Mail\QuoteReceivedMail;
use Illuminate\Support\Facades\Mail;

class QuoteController extends Controller
{
    // View customer's own quote requests
    public function index(): JsonResponse
    {
        $customer = Auth::user();
        $quotes = Quote::with('package')->where('customer_id', $customer->id)->orderBy('created_at', 'desc')->get();

        return response()->json($quotes);
    }

    // Create a new quote request
    public function store(Request $request): JsonResponse
    {
        $customer = $request->user();

        $request->validate([
            'package_id' => 'required|exists:packages,id',
            'start_date' => 'required|date|after:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'number_of_people' => 'required|integer|min:1',
            'special_requests' => 'nullable|string|max:1000'
        ]);

        $quote = Quote::create([
            'customer_id' => $customer->id,
            'package_id' => $request->package_id,
            'number_of_people' => $request->number_of_people,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'special_requests' => $request->special_requests
        ]);

        Mail::to($customer->email)->send(new QuoteReceivedMail($quote->load('package', 'customer')));

        return response()->json([
            'message' => 'Quote request submitted. You will receive an email confirmation shortly.',
            'data' => $quote
        ], 201);
    }
}
