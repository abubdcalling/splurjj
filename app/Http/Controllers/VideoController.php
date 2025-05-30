<?php

namespace App\Http\Controllers;

use App\Models\Video;
use Illuminate\Http\Request;

class VideoController extends Controller
{
    // GET /api/videos
    public function index(Request $request)
    {
        try {
            $videos = Video::when($request->search, function ($query, $search) {
                return $query->where('heading', 'like', "%{$search}%")
                    ->orWhere('sub_heading', 'like', "%{$search}%")
                    ->orWhere('tags', 'like', "%{$search}%");
            })
                ->latest()
                ->paginate(10);

            // Attach full URL for image_1
            $videos->getCollection()->transform(function ($video) {
                $video->image_1 = $video->image_1 ? url('uploads/videos/' . $video->image_1) : null;
                return $video;
            });

            return response()->json([
                'success' => true,
                'data'    => $videos
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch videos',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    



    // GET /api/videos/{id}
    public function show($id)
    {
        try {
            $video = Video::findOrFail($id);
            $video->image_1 = $video->image_1 ? url('uploads/videos/' . $video->image_1) : null;

            return response()->json([
                'success' => true,
                'data'    => $video
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Video not found',
                'error'   => $e->getMessage()
            ], 404);
        }
    }

    // POST /api/videos
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'heading'       => 'nullable|string',
                'credits'       => 'nullable|string',
                'date'          => 'nullable|date',
                'sub_heading'   => 'nullable|string',
                'body'          => 'nullable|string',
                'image_1'       => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:10240', // image validation
                'advertising'   => 'nullable|string',
                'tags'          => 'nullable|string',
                'category_id'   => 'required|exists:categories,id',
                'subcategory_id' => 'required|exists:subcategories,id',
            ]);

            // Handle image upload
            $imageName = null;
            if ($request->hasFile('image_1')) {
                $file = $request->file('image_1');
                $imageName = time() . '_video_image.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/videos'), $imageName);
            }

            $video = Video::create(array_merge(
                $validated,
                ['image_1' => $imageName]
            ));

            $video->image_1 = $video->image_1 ? url('uploads/videos/' . $video->image_1) : null;

            return response()->json([
                'success' => true,
                'message' => 'Video created successfully',
                'data'    => $video
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $ve) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors'  => $ve->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create video',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    // PUT /api/videos/{id}
    public function update(Request $request, $id)
    {
        try {
            $video = Video::findOrFail($id);

            $validated = $request->validate([
                'heading'       => 'sometimes|nullable|string',
                'credits'       => 'sometimes|nullable|string',
                'date'          => 'sometimes|nullable|date',
                'sub_heading'   => 'sometimes|nullable|string',
                'body'          => 'sometimes|nullable|string',
                'image_1'       => 'sometimes|nullable|image|mimes:jpg,jpeg,png,gif,webp|max:10240',
                'advertising'   => 'sometimes|nullable|string',
                'tags'          => 'sometimes|nullable|string',
                'category_id'   => 'sometimes|required|exists:categories,id',
                'subcategory_id' => 'sometimes|required|exists:subcategories,id',
            ]);

            if ($request->hasFile('image_1')) {
                $file = $request->file('image_1');
                $imageName = time() . '_video_image.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/videos'), $imageName);
                $validated['image_1'] = $imageName;
            }

            $video->update($validated);

            $video->image_1 = $video->image_1 ? url('uploads/videos/' . $video->image_1) : null;

            return response()->json([
                'success' => true,
                'message' => 'Video updated successfully',
                'data'    => $video
            ]);
        } catch (\Illuminate\Validation\ValidationException $ve) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors'  => $ve->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update video',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    // DELETE /api/videos/{id}
    public function destroy($id)
    {
        try {
            $video = Video::findOrFail($id);
            $video->delete();

            return response()->json([
                'success' => true,
                'message' => 'Video deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete video',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
