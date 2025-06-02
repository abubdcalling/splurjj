<?php

namespace App\Http\Controllers;

use App\Models\Content;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;


class ContentController extends Controller
{

    public function indexFrontend($cat_id)
    {
        try {
            // Get all contents where category_id matches
            $contents = Content::with(['category', 'subcategory'])
                ->where('category_id', $cat_id)
                ->latest()
                ->get();

            return response()->json([
                'status' => true,
                'message' => 'Contents fetched successfully.',
                'data' => $contents,
            ]);
        } catch (\Exception $e) {
            Log::error('Fetching contents failed: ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Failed to fetch contents.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }



    public function index($cat_id, $sub_id, $id)
    {
        try {
            $content = Content::where('category_id', $cat_id)
                ->where('subcategory_id', $sub_id)
                ->where('id', $id)
                ->firstOrFail();

            return response()->json([
                'status' => true,
                'message' => 'Content fetched successfully.',
                'data' => $content,
            ]);
        } catch (\Exception $e) {
            Log::error('Content fetch failed: ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Failed to fetch content.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }



    public function indexForSubCategory($cat_id, $sub_id)
    {
        try {
            // Optionally, verify category and subcategory exist (you can skip if already validated by route)
            // Category::findOrFail($cat_id);
            // SubCategory::where('category_id', $cat_id)->findOrFail($sub_id);

            $contents = Content::where('category_id', $cat_id)
                ->where('subcategory_id', $sub_id)
                ->orderBy('date', 'desc')  // example ordering by date desc
                ->get();

            return response()->json([
                'status' => true,
                'data' => $contents,
            ]);
        } catch (\Exception $e) {
            Log::error('Fetching contents by category and subcategory failed: ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Failed to fetch contents.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }



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

    public function update(Request $request, $id)
    {
        // Find the content or fail
        $content = Content::findOrFail($id);

        // Validate all except tags
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'subcategory_id' => 'required|exists:sub_categories,id',
            'heading' => 'nullable|string',
            'author' => 'nullable|string',
            'date' => 'nullable|date',
            'sub_heading' => 'nullable|string',
            'body1' => 'nullable|string',
            'image1' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10248',
            'advertising_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10248',
            // omit tags intentionally
        ]);

        try {
            // Handle new image1 upload if provided
            if ($request->hasFile('image1')) {
                $image1Path = $request->file('image1')->store('images', 'public');
                $validated['image1'] = $image1Path;

                // Optionally, delete old image here if needed
                // Storage::disk('public')->delete($content->image1);
            }

            // Handle new advertising_image upload if provided
            if ($request->hasFile('advertising_image')) {
                $advertisingImagePath = $request->file('advertising_image')->store('images', 'public');
                $validated['advertising_image'] = $advertisingImagePath;

                // Optionally, delete old advertising image here if needed
                // Storage::disk('public')->delete($content->advertising_image);
            }

            // Handle tags separately outside validation
            $tagsInput = $request->input('tags');

            if (is_string($tagsInput)) {
                $tagsArray = array_filter(array_map('trim', explode(',', $tagsInput)));
            } elseif (is_array($tagsInput)) {
                $tagsArray = $tagsInput;
            } else {
                $tagsArray = null;
            }

            $validated['tags'] = $tagsArray;

            // Update the content with validated data
            $content->update($validated);

            return response()->json([
                'status' => true,
                'message' => 'Content updated successfully.',
                'data' => $content,
            ]);
        } catch (\Exception $e) {
            \Log::error('Content update failed: ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Failed to update content.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $content = Content::findOrFail($id);

            // Optional: delete images from storage
            if ($content->image1) {
                Storage::disk('public')->delete($content->image1);
            }
            if ($content->advertising_image) {
                Storage::disk('public')->delete($content->advertising_image);
            }

            $content->delete();

            return response()->json([
                'status' => true,
                'message' => 'Content deleted successfully.',
            ]);
        } catch (\Exception $e) {
            Log::error('Content deletion failed: ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Failed to delete content.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
