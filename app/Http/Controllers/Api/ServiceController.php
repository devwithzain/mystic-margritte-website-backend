<?php

namespace App\Http\Controllers\Api;

use App\Models\Service;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Requests\ServiceRequest;
use App\Http\Controllers\Controller;
use App\Http\Resources\ServiceResource;
use Illuminate\Support\Facades\Storage;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::all();
        return response()->json([
            'services' => ServiceResource::collection($services)
        ], 200);
    }
    public function store(ServiceRequest $request)
    {
        $validatedData = $request->validated();
        $image = $request->file('image');
        $imageName = 'services/' . Str::random(32) . '.' . $image->getClientOriginalExtension();
        Storage::disk('public')->put($imageName, file_get_contents($image));
        $service = Service::create([
            'title' => $validatedData['title'],
            'description' => $validatedData['description'],
            'price' => $validatedData['price'],
            'image' => $imageName,
        ]);
        return response()->json([
            'message' => 'Service created successfully.',
            'service' => $service
        ]);
    }
    public function show(string $id)
    {
        $service = Service::find($id);

        if (!$service) {
            return response()->json([
                'error' => 'Service not found.'
            ], 404);
        }

        return response()->json([
            'service' => new ServiceResource($service)
        ], 200);
    }
    public function update(Request $request, string $id)
    {
        try {
            $service = Service::find($id);
            if (!$service) {
                return response()->json([
                    'error' => 'Not Found.'
                ], 404);
            }

            $validatedData = $request->validate([
                'title' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'price' => 'nullable|string',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
            ]);

            // Update fields only if provided
            if ($request->has('title')) {
                $service->title = $validatedData['title'];
            }
            if ($request->has('description')) {
                $service->description = $validatedData['description'];
            }
            if ($request->has('price')) {
                $service->price = $validatedData['price'];
            }

            // Handle image update
            if ($request->hasFile('image')) {
                $storage = Storage::disk('public');

                // Delete old image if exists
                if ($service->image && $storage->exists($service->image)) {
                    $storage->delete($service->image);
                }

                // Store new image
                $imageName = 'blogs/' . Str::random(32) . '.' . $request->image->getClientOriginalExtension();
                $storage->put($imageName, file_get_contents($request->image->getRealPath()));

                $service->image = $imageName;
            }

            // Save changes
            $service->save();

            return response()->json([
                'success' => true,
                'message' => 'Service updated successfully.',
                'service' => $service
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
        $service = Service::find($id);
        if (!$service) {
            return response()->json([
                'error' => 'Service Not Found.'
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