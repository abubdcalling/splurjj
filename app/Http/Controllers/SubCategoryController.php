<?php

namespace App\Http\Controllers;

use App\Models\SubCategory;
use Illuminate\Http\Request;

class SubCategoryController extends Controller
{
    /**
     * Display a listing of the subcategories.
     */
    public function index()
    {
        $subcategories = SubCategory::select('category_id', 'name')->get();

        $grouped = $subcategories->groupBy('category_id')->map(function ($items) {
            return $items->map(function ($item) {
                return ['name' => $item->name];
            });
        });

        return response()->json([
            'success' => true,
            'data' => $grouped
        ]);
    }



    /**
     * Store a newly created subcategory in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
        ]);

        $subcategory = SubCategory::create($validated);

        return response()->json($subcategory, 201);
    }

    /**
     * Display the specified subcategory.
     */
    public function show(SubCategory $subcategory)
    {
        return $subcategory;
    }

    /**
     * Update the specified subcategory in storage.
     */
    public function update(Request $request, SubCategory $subcategory)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'category_id' => 'sometimes|required|exists:categories,id',
        ]);

        $subcategory->update($validated);

        return response()->json($subcategory);
    }

    /**
     * Remove the specified subcategory from storage.
     */
    public function destroy(SubCategory $subcategory)
    {
        $subcategory->delete();

        return response()->json(['message' => 'Subcategory deleted successfully']);
    }
}
