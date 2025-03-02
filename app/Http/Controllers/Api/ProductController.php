<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductResource;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        return response()->json([
            'products' => ProductResource::collection(Product::all())
        ], 200);
    }
    public function store(ProductRequest $request)
    {
        try {
            $validatedData = $request->validated();
            $imagePaths = [];

            if ($request->hasFile('image')) {
                foreach ($request->file('image') as $image) {
                    $imageName = 'products/' . Str::random(32) . '.' . $image->getClientOriginalExtension();
                    Storage::disk('public')->put($imageName, file_get_contents($image));
                    $imagePaths[] = $imageName;
                }
            }

            $product = Product::create([
                'title' => $validatedData['title'],
                'description' => $validatedData['description'],
                'price' => $validatedData['price'],

                'image' => !empty($imagePaths) ? json_encode($imagePaths) : null,
            ]);
            return response()->json([
                'message' => 'Product created successfully.',
                'product' => new ProductResource($product)
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    public function show(string $id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['error' => 'Product Not Found.'], 404);
        }
        return response()->json(['product' => new ProductResource($product)], 200);
    }
    public function update(Request $request, string $id)
    {
        try {
            $product = Product::find($id);
            if (!$product) {
                return response()->json(['error' => 'Product Not Found.'], 404);
            }

            $validated = $request->validate([
                'title' => 'nullable|string|max:255',
                'price' => 'nullable|string',
                'description' => 'nullable|string',
                'image' => 'nullable',
            ]);

            if ($request->has('title')) {
                $product->title = $validated['title'];
            }
            if ($request->has('description')) {
                $product->description = $validated['description'];
            }
            if ($request->has('price')) {
                $product->price = $validated['price'];
            }

            $imagePaths = json_decode($product->image, true) ?? [];

            if ($request->has('images_to_delete')) {
                $imagesToDelete = json_decode($request->input('images_to_delete'), true);
                foreach ($imagesToDelete as $imageUrl) {
                    $imagePath = str_replace(env('APP_URL') . '/storage/', '', $imageUrl);
                    if (Storage::disk('public')->exists($imagePath)) {
                        Storage::disk('public')->delete($imagePath);
                    }
                    $imagePaths = array_filter($imagePaths, fn($path) => $path !== $imagePath);
                }

                $imagePaths = array_values($imagePaths);
            }

            if ($request->hasFile('image')) {
                foreach ($request->file('image') as $image) {
                    $imageName = 'products/' . Str::random(32) . '.' . $image->getClientOriginalExtension();
                    Storage::disk('public')->put($imageName, file_get_contents($image));
                    $imagePaths[] = $imageName; // Append new image to the existing array
                }
            }

            $product->image = json_encode($imagePaths);

            $product->save();

            return response()->json([
                'message' => "Product successfully updated.",
                'product' => new ProductResource($product)
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => "Something went wrong!",
                'message' => $e->getMessage()
            ], 500);
        }
    }
    public function destroy(string $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'error' => 'Product Not Found.'
            ], 404);
        }

        $storage = Storage::disk('public');

        $images = json_decode($product->image, true);

        if (!empty($images)) {
            foreach ($images as $imagePath) {
                if ($storage->exists($imagePath)) {
                    $storage->delete($imagePath);
                }
            }
        }

        $product->delete();

        return response()->json([
            'success' => "Product successfully deleted."
        ], 200);
    }
}