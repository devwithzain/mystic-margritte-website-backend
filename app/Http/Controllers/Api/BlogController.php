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
        $services = Blog::all();
        return response()->json([
            'data' => BlogResource::collection($services)
        ], 200);
    }
    public function store(BlogRequest $request)
    {
        $validatedData = $request->validated();
        $image = $request->file('image');
        $imageName = 'services/' . Str::random(32) . '.' . $image->getClientOriginalExtension();
        Storage::disk('public')->put($imageName, file_get_contents($image));
        $service = Blog::create([
            'title' => $validatedData['title'],
            'description' => $validatedData['description'],
            'short_description' => $validatedData['short_description'],
            'image' => $imageName,
        ]);
        return response()->json([
            'message' => 'Service created successfully.',
            'service' => $service
        ]);
    }
    public function show(string $id)
    {
        $service = Blog::find($id);
        $blog = Blog::with('comments.user')->find($id);

        if (!$service) {
            return response()->json([
                'error' => 'Not Found.'
            ], 404);
        }

        if (!$blog) {
            return response()->json([
                'error' => 'Blog not found.'
            ], 404);
        }

        return response()->json([
            'data' => new BlogResource($blog)
        ], 200);
    }
    public function update(BlogRequest $request, string $id)
    {
        $service = Blog::find($id);
        if (!$service) {
            return response()->json([
                'error' => 'Not Found.'
            ], 404);
        }
        $validatedData = $request->validated();
        $service->title = $validatedData['title'];
        $service->description = $validatedData['description'];
        $service->short_description = $validatedData['short_description'];

        if ($request->hasFile('image')) {
            if ($service->image) {
                Storage::disk('public')->delete($service->image);
            }
            $image = $request->file('image');
            $imageName = 'services/' . Str::random(32) . '.' . $image->getClientOriginalExtension();
            Storage::disk('public')->put($imageName, file_get_contents($image));

            $service->image = $imageName;
        }
        $service->save();
        return response()->json([
            'message' => 'Service updated successfully.',
            'service' => $service
        ]);

    }
    public function destroy(string $id)
    {
        $service = Blog::find($id);
        if (!$service) {
            return response()->json([
                'error' => 'Not Found.'
            ], 404);
        }
        if ($service->image) {
            Storage::disk('public')->delete($service->image);
        }
        $service->delete();
        return response()->json([
            'message' => 'Service deleted successfully.'
        ]);
    }
}