<?php

namespace App\Http\Controllers;

use App\Models\Content;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ContentController extends Controller
{
   

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'subcategory_id' => 'required|exists:sub_categories,id',
            'heading' => 'required|string',
            'author' => 'required|string',
            'date' => 'required|date',
            'sub_heading' => 'nullable|string',
            'body1' => 'required|string',
            'image1' => 'required|string',
            'advertising_image' => 'required|string',
            'tags' => 'nullable|array',
        ]);

        try {
            // Encode tags to JSON if present
            if (isset($validated['tags']) && is_array($validated['tags'])) {
                $validated['tags'] = json_encode($validated['tags']);
            }

            $content = Content::create($validated);

            return response()->json([
                'status' => true,
                'message' => 'Content created successfully.',
                'data' => $content,
            ], 201);
        } catch (\Exception $e) {
            Log::error('Content creation failed: ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Failed to create content.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
