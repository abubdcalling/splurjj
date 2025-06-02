<?php

namespace App\Http\Controllers;

use App\Models\Content;
use Illuminate\Http\Request;

class ContentController extends Controller
{


    public function index($cat_id, $sub_id, $id)
    {
        try {
            $content = Content::where('id', $id)
                ->where('category_id', $cat_id)
                ->where('subcategory_id', $sub_id)
                ->firstOrFail();

            return response()->json([
                'success' => true,
                'data' => $content
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Content not found for the specified category and subcategory.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving content.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function indexFrontend($cat_id)
    {
        try {
            $contents = Content::where('category_id', $cat_id)
                ->orderBy('created_at', 'desc')
                ->limit(4)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $contents
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving contents.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function indexForSubCategory($cat_id, $sub_id)
    {
        try {
            $contents = Content::where('category_id', $cat_id)
                ->where('subcategory_id', $sub_id)
                ->orderBy('created_at', 'desc')
                ->paginate(10); // paginate with 10 per page

            return response()->json([
                'success' => true,
                'data' => $contents
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving contents for the subcategory.',
                'error' => $e->getMessage()
            ], 500);
        }
    }






    public function store(Request $request)
    {
        try {
            // Optional: decode tags if sent as a JSON string
            if (is_string($request->tags)) {
                $request->merge([
                    'tags' => json_decode($request->tags, true)
                ]);
            }

            $validated = $request->validate([
                'category_id' => 'required|exists:categories,id',
                'subcategory_id' => 'required|exists:sub_categories,id',
                'heading' => 'nullable|string',
                'credits' => 'nullable|string',
                'date' => 'nullable|date',
                'sub_heading' => 'nullable|string',
                'body' => 'nullable|string',
                'image_1' => 'nullable|string', // Or file if you're uploading
                'advertising' => 'nullable|string',
                'tags' => 'nullable|array',
            ]);

            $content = Content::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Content created successfully.',
                'data' => $content
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while creating content.',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function update(Request $request, $id)
    {
        try {
            $content = Content::findOrFail($id);

            // Decode tags if sent as a JSON string
            if (is_string($request->tags)) {
                $request->merge([
                    'tags' => json_decode($request->tags, true)
                ]);
            }

            $validated = $request->validate([
                'category_id' => 'sometimes|exists:categories,id',
                'subcategory_id' => 'sometimes|exists:subcategories,id',
                'heading' => 'nullable|string',
                'credits' => 'nullable|string',
                'date' => 'nullable|date',
                'sub_heading' => 'nullable|string',
                'body' => 'nullable|string',
                'image_1' => 'nullable|string',
                'advertising' => 'nullable|string',
                'tags' => 'nullable|array',
            ]);

            $content->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Content updated successfully.',
                'data' => $content
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Content not found.'
            ], 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating content.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $content = Content::findOrFail($id);
            $content->delete();

            return response()->json([
                'success' => true,
                'message' => 'Content deleted successfully.'
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Content not found.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while deleting content.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
