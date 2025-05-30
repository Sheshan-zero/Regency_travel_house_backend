<?php

namespace App\Http\Controllers;

use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageController extends Controller
{
    public function index()
    {
        return response()->json(Image::with('package')->get());
    }


    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:2048',
            'section' => 'nullable|string',
            'package_id' => 'nullable|exists:packages,id',
            'destination_id' => 'nullable|exists:destinations,id',
            'booking_id' => 'nullable|exists:bookings,id'
        ]);

        $file = $request->file('image');
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $file->storeAs('public/images', $filename); // Correct path

        // Set section automatically if not provided
        $section = $request->section;
        if (!$section) {
            if ($request->has('destination_id')) {
                $section = 'hero';
            } elseif ($request->has('package_id')) {
                $section = 'gallery';
            } elseif ($request->has('booking_id')) {
                $section = 'recipt';
            }
        }

        $image = Image::create([
            'filename' => $filename,
            'section' => $section,
            'package_id' => $request->package_id,
            'destination_id' => $request->destination_id,
            'booking_id' => $request->booking_id,
        ]);

        return response()->json([
            'message' => 'Image uploaded successfully',
            'image' => $image
        ]);
    }

    // In ImageController
    public function storeMultiple(Request $request)
    {
        $request->validate([
            'images.*' => 'required|image|max:2048',
            'package_id' => 'required|exists:packages,id',
        ]);

        $uploaded = [];

        foreach ($request->file('images') as $file) {
            $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/images', $filename);

            $image = Image::create([
                'filename' => $filename,
                'package_id' => $request->package_id,
                'section' => 'gallery',
            ]);

            $uploaded[] = $image;
        }

        return response()->json(['message' => 'Images uploaded', 'images' => $uploaded]);
    }


    public function show($id)
    {
        $image = Image::with('package')->findOrFail($id);
        return response()->json($image);
    }

    public function destroy($id)
    {
        $image = Image::findOrFail($id);
        Storage::delete('public/images/' . $image->filename);
        $image->delete();

        return response()->json(['message' => 'Image deleted']);
    }


    public function getImagesByDestination($destination_id)
    {
        $images = Image::where('destination_id', $destination_id)
            ->where('section', 'hero')
            ->get();
        return response()->json($images);
    }

    public function getImagesByPackage($package_id)
    {
        $images = Image::where('package_id', $package_id)
            ->where('section', 'gallery')
            ->get();

        return response()->json($images);
    }
}
