<?php

namespace App\Http\Controllers\Api;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

    class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        return response()->json([
            'categories' => $categories,
        ]);
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255|unique:categories,title',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation Error',
                'messages' => $validator->errors(),
            ], 422);
        }

        $category = Category::create([
            'title' => $request->title,
        ]);

        return response()->json([
            'message' => 'Category created successfully.',
            'category' => $category,
        ], 201);
    }
    public function show($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'error' => 'Category not found.',
            ], 404);
        }

        return response()->json([
            'category' => $category,
        ]);
    }
    public function update(Request $request, $id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'error' => 'Category not found.',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255|unique:categories,title,' . $id,
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation Error',
                'messages' => $validator->errors(),
            ], 422);
        }

        $category->update([
            'title' => $request->title,
        ]);

        return response()->json([
            'message' => 'Category updated successfully.',
            'category' => $category,
        ]);
    }
    public function destroy($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'error' => 'Category not found.',
            ], 404);
        }

        $category->delete();

        return response()->json([
            'message' => 'Category deleted successfully.',
        ]);
    }
}