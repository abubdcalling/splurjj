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
        $validated = $request->validate([
            'category_id'     => 'required|exists:categories,id',
            'subcategory_id'  => 'required|exists:sub_categores,id',
            'heading'         => 'nullable|string',
            'credits'         => 'nullable|string',
            'date'            => 'nullable|date',
            'sub_heading'     => 'nullable|string',
            'body'            => 'nullable|string',
            'image_1'         => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'advertising'     => 'nullable|string',
            'tags'            => 'nullable|array',
            'tags.*'          => 'string',
        ]);

        if ($request->hasFile('image_1')) {
            $validated['image_1'] = $request->file('image_1')->store('contents/images', 'public');
        }

        // No need to json_encode here because of model casting

        $content = Content::create($validated);

        return response()->json([
            'message' => 'Content created successfully.',
            'content' => $content,
        ], 201);
    }
}
