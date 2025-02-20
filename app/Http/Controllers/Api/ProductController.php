<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use Illuminate\Support\Str;
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
                'shortDescription' => $validatedData['shortDescription'],
                'color' => $validatedData['color'],
                'category' => $validatedData['category'],
                'size' => $validatedData['size'],
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
    public function update(ProductRequest $request, string $id)
    {
        try {
            $product = Product::find($id);
            if (!$product) {
                return response()->json(['error' => 'Product Not Found.'], 404);
            }

            $validated = $request->validated();
            $imagePaths = json_decode($product->image, true) ?? [];

            if ($request->hasFile('image')) {
                // Delete old images
                foreach ($imagePaths as $oldImage) {
                    if (Storage::disk('public')->exists($oldImage)) {
                        Storage::disk('public')->delete($oldImage);
                    }
                }

                // Upload new images
                $newImagePaths = [];
                foreach ($request->file('image') as $image) {
                    $imageName = 'products/' . Str::random(32) . '.' . $image->getClientOriginalExtension();
                    Storage::disk('public')->put($imageName, file_get_contents($image));
                    $newImagePaths[] = $imageName;
                }

                $validated['image'] = !empty($newImagePaths) ? json_encode($newImagePaths) : null;
            }

            $product->update($validated);

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