<?php

namespace App\Http\Controllers;

use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class PackageController extends Controller
{
    private function isAdmin(): bool
    {
        return Auth::guard('staff')->user()?->role === 'Admin';
    }

    public function index(): JsonResponse
    {
        $packages = Package::with('destination')->get();
        return response()->json($packages);
    }

    public function store(Request $request): JsonResponse
    {
        if (!$this->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'destination_id' => 'required|exists:destinations,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'activities' => 'nullable|string',
            'include' => 'nullable|string',
            'exclude' => 'nullable|string',
            'price_per_person' => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:1',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'available_slots' => 'required|integer|min:0',
            'image_url' => 'nullable|url',
            'is_featured' => 'boolean',
            'category' => 'nullable|string'
        ]);

        $package = Package::create($validated);
        return response()->json($package, 201);
    }

    // public function show(int $id): JsonResponse
    // {
    //     $package = Package::with('destination')->find($id);
    //     if (!$package) {
    //         return response()->json(['message' => 'Package not found'], 404);
    //     }
    //     return response()->json($package);
    // }

    public function show(int $id): JsonResponse
    {
        $package = Package::with(['destination', 'itineraries'])->find($id);
        if (!$package) {
            return response()->json(['message' => 'Package not found'], 404);
        }

        return response()->json($package);
    }


    public function update(Request $request, int $id): JsonResponse
    {
        if (!$this->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $package = Package::find($id);

        if (!$package) {
            return response()->json(['message' => 'Package not found'], 404);
        }

        $validated = $request->validate([
            'destination_id' => 'sometimes|exists:destinations,id',
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'price_per_person' => 'sometimes|numeric|min:0',
            'duration_days' => 'sometimes|integer|min:1',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'available_slots' => 'sometimes|integer|min:0',
            'image_url' => 'nullable|url',
            'is_featured' => 'boolean',
            'category' => 'nullable|string'
        ]);

        $package->update($validated);
        return response()->json($package);
    }

    public function destroy(int $id): JsonResponse
    {
        if (!$this->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $package = Package::find($id);

        if (!$package) {
            return response()->json(['message' => 'Package not found'], 404);
        }

        $package->delete();
        return response()->json(['message' => 'Package deleted']);
    }


    public function categorizedPackages(): JsonResponse
    {
        $packages = Package::all()->groupBy('category');

        return response()->json($packages);
    }

    public function getByCategory(string $category): JsonResponse
    {
        $packages = Package::where('category', $category)->with('destination')->get();

        if ($packages->isEmpty()) {
            return response()->json(['message' => 'No packages found for this category'], 404);
        }

        return response()->json($packages);
    }

    public function smartSearch(Request $request)
    {
        $keyword = $request->input('q');

        if (!$keyword) {
            return response()->json(['message' => 'Please provide a search query.'], 400);
        }

        $keywords = explode(' ', $keyword);

        $results = \App\Models\Package::with('destination')
            ->where(function ($query) use ($keywords) {
                foreach ($keywords as $word) {
                    $query->where(function ($subQuery) use ($word) {
                        $subQuery->where('title', 'like', "%{$word}%")
                            ->orWhere('category', 'like', "%{$word}%")
                            ->orWhere('activities', 'like', "%{$word}%")
                            ->orWhereHas('destination', function ($q) use ($word) {
                                $q->where('country', 'like', "%{$word}%");
                            });
                    });
                }
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($results);
    }
}
