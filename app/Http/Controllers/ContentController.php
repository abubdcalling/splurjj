<?php

namespace App\Http\Controllers;

use App\Models\Content;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ContentController extends Controller
{


    // public function store(Request $request)
    // {
    //     $validated = $request->validate([
    //         'category_id' => 'required|exists:categories,id',
    //         'subcategory_id' => 'required|exists:sub_categories,id',
    //         'heading' => 'nullable|string',
    //         'author' => 'nullable|string',
    //         'date' => 'nullable|date',
    //         'sub_heading' => 'nullable|string',
    //         'body1' => 'nullable|string',
    //         'image1' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:10248',
    //         'advertising_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:10248',
    //         'tags' => 'nullable|array',
    //     ]);

    //     try {
    //         // Safely encode tags if provided as array
    //         $validated['tags'] = isset($validated['tags']) ? json_encode($validated['tags']) : null;

    //         $content = Content::create($validated);

    //         return response()->json([
    //             'status' => true,
    //             'message' => 'Content created successfully.',
    //             'data' => $content,
    //         ], 201);
    //     } catch (\Exception $e) {
    //         Log::error('Content creation failed: ' . $e->getMessage());

    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Failed to create content.',
    //             'error' => $e->getMessage(),
    //         ], 500);
    //     }
    // }

    public function store(Request $request)
{
    // Validate everything except tags (which we'll handle separately)
    $validated = $request->validate([
        'category_id' => 'required|exists:categories,id',
        'subcategory_id' => 'required|exists:sub_categories,id',
        'heading' => 'nullable|string',
        'author' => 'nullable|string',
        'date' => 'nullable|date',
        'sub_heading' => 'nullable|string',
        'body1' => 'nullable|string',
        'image1' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:10248',
        'advertising_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:10248',
        // omit tags here intentionally
    ]);

    try {
        // Handle image uploads
        if ($request->hasFile('image1')) {
            $image1Path = $request->file('image1')->store('images', 'public');
            $validated['image1'] = $image1Path;
        }

        if ($request->hasFile('advertising_image')) {
            $advertisingImagePath = $request->file('advertising_image')->store('images', 'public');
            $validated['advertising_image'] = $advertisingImagePath;
        }

        // Handle tags separately outside validation
        $tagsInput = $request->input('tags');

        if (is_string($tagsInput)) {
            // if tags come as a comma-separated string, convert to array
            $tagsArray = array_filter(array_map('trim', explode(',', $tagsInput)));
        } elseif (is_array($tagsInput)) {
            $tagsArray = $tagsInput;
        } else {
            $tagsArray = null;
        }

        $validated['tags'] = $tagsArray;

        $content = Content::create($validated);

        return response()->json([
            'status' => true,
            'message' => 'Content created successfully.',
            'data' => $content,
        ], 201);
    } catch (\Exception $e) {
        \Log::error('Content creation failed: ' . $e->getMessage());

        return response()->json([
            'status' => false,
            'message' => 'Failed to create content.',
            'error' => $e->getMessage(),
        ], 500);
    }
}


    
}
