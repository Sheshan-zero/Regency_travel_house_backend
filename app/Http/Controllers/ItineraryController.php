<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Itinerary;
use App\Models\Package;
use Illuminate\Http\JsonResponse;

class ItineraryController extends Controller
{
    // View all itineraries for a given package
    public function index($package_id): JsonResponse
    {
        $itineraries = Itinerary::where('package_id', $package_id)
            ->orderBy('day_number')
            ->get();

        return response()->json($itineraries);
    }

    // Add a new itinerary item
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'package_id' => 'required|exists:packages,id',
            'day_number' => 'required|integer|min:1',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string'
        ]);

        $itinerary = Itinerary::create($request->all());

        return response()->json(['message' => 'Itinerary created', 'data' => $itinerary], 201);
    }

    // Update itinerary
    public function update(Request $request, $id): JsonResponse
    {
        $itinerary = Itinerary::findOrFail($id);

        $request->validate([
            'day_number' => 'nullable|integer|min:1',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'location' => 'required|string'
        ]);

        $itinerary->update($request->only(['day', 'title', 'description', 'location']));

        return response()->json(['message' => 'Itinerary updated', 'data' => $itinerary]);
    }

    // Delete itinerary
    public function destroy($id): JsonResponse
    {
        $itinerary = Itinerary::findOrFail($id);
        $itinerary->delete();

        return response()->json(['message' => 'Itinerary deleted']);
    }

    // Fetch Itineraries for Map
    public function mapData($packageId): JsonResponse
    {
        $itineraries = Itinerary::where('package_id', $packageId)
            ->orderBy('day_number')
            ->get();

        return response()->json($itineraries);
    }

}
