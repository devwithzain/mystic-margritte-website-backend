<?php

namespace App\Http\Controllers\Api;

use App\Models\Blog;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Requests\BlogRequest;
use App\Http\Controllers\Controller;
use App\Http\Resources\BlogResource;
use Illuminate\Support\Facades\Storage;

class BlogController extends Controller
{
    public function index()
    {
        $blogs = Blog::all();
        return response()->json([
            'blogs' => BlogResource::collection($blogs)
        ], 200);
    }
    public function store(BlogRequest $request)
    {
        $validatedData = $request->validated();
        $image = $request->file('image');
        $imageName = 'blogs/' . Str::random(32) . '.' . $image->getClientOriginalExtension();
        Storage::disk('public')->put($imageName, file_get_contents($image));
        $blog = Blog::create([
            'title' => $validatedData['title'],
            'description' => $validatedData['description'],
            'short_description' => $validatedData['short_description'],
            'category' => $validatedData['category'],
            'image' => $imageName,
        ]);
        return response()->json([
            'message' => 'Blog created successfully.',
            'service' => $blog
        ]);
    }
    public function show(string $id)
    {
        $blog = Blog::find($id);
        $blog = Blog::with('comments.user')->find($id);

        if (!$blog) {
            return response()->json([
                'error' => 'Blog not found.'
            ], 404);
        }

        return response()->json([
            'blog' => new BlogResource($blog)
        ], 200);
    }
    public function update(Request $request, string $id)
    {
        try {
            $blog = Blog::find($id);
            if (!$blog) {
                return response()->json([
                    'error' => 'Not Found.'
                ], 404);
            }

            // Validate incoming request data
            $validatedData = $request->validate([
                'title' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'short_description' => 'nullable|string',
                'category' => 'nullable|string|max:255',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
            ]);

            // Update fields only if provided
            if ($request->has('title')) {
                $blog->title = $validatedData['title'];
            }
            if ($request->has('description')) {
                $blog->description = $validatedData['description'];
            }
            if ($request->has('short_description')) {
                $blog->short_description = $validatedData['short_description'];
            }
            if ($request->has('category')) {
                $blog->category = $validatedData['category'];
            }

            // Handle image update
            if ($request->hasFile('image')) {
                $storage = Storage::disk('public');

                // Delete old image if exists
                if ($blog->image && $storage->exists($blog->image)) {
                    $storage->delete($blog->image);
                }

                // Store new image
                $imageName = 'blogs/' . Str::random(32) . '.' . $request->image->getClientOriginalExtension();
                $storage->put($imageName, file_get_contents($request->image->getRealPath()));

                $blog->image = $imageName;
            }

            // Save changes
            $blog->save();

            return response()->json([
                'success' => true,
                'message' => 'Blog updated successfully.',
                'blog' => $blog
            ], 200);

        } catch (\Exception $e) {
            \Log::error('Error updating blog: ' . $e->getMessage(), ['exception' => $e]);

            return response()->json([
                'message' => "Something went wrong!",
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function destroy(string $id)
    {
        $blog = Blog::find($id);
        if (!$blog) {
            return response()->json([
                'error' => 'Blog Not Found.'
            ], 404);
        }
        if ($blog->image) {
            Storage::disk('public')->delete($blog->image);
        }
        $blog->delete();
        return response()->json([
            'message' => 'Blog deleted successfully.'
        ]);
    }
}