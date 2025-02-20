<?php

namespace App\Http\Controllers\Api;

use App\Models\Blog;
use Illuminate\Support\Str;
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
    public function update(BlogRequest $request, string $id)
    {
        $blog = Blog::find($id);
        if (!$blog) {
            return response()->json([
                'error' => 'Not Found.'
            ], 404);
        }
        $validatedData = $request->validated();
        $blog->title = $validatedData['title'];
        $blog->description = $validatedData['description'];
        $blog->short_description = $validatedData['short_description'];

        if ($request->hasFile('image')) {
            if ($blog->image) {
                Storage::disk('public')->delete($blog->image);
            }
            $image = $request->file('image');
            $imageName = 'blogs/' . Str::random(32) . '.' . $image->getClientOriginalExtension();
            Storage::disk('public')->put($imageName, file_get_contents($image));

            $blog->image = $imageName;
        }
        $blog->save();
        return response()->json([
            'message' => 'Blog updated successfully.',
            'Blog' => $blog
        ]);

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