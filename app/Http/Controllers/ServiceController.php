<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\User; // Needed for policy check
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Auth\Access\AuthorizationException; // For manual policy denial messages
use App\Http\Requests\Service\StoreServiceRequest; // Import
use App\Http\Requests\Service\UpdateServiceRequest; // Import
use App\Http\Requests\Service\UploadServiceMediaRequest; // Import
use Illuminate\Support\Facades\Log; // For logging file operations

class ServiceController extends Controller
{
    public function __construct()
    {
        // Apply policies automatically for resource methods
        $this->authorizeResource(Service::class, 'service');
    }

    /**
     * Display a listing of services.
     * Publicly accessible, with filters.
     */
    public function index(Request $request)
    {
        $query = Service::query()->where('is_active', true); // Only active services by default
        
        $filterUserId = $request->input('filter.user_id'); // Accesses filter[user_id]
        if ($filterUserId) { // If it exists and is not empty
            $query->where('user_id', $filterUserId);
        }

        // Implement filtering
        if ($request->has('category')) {
            $query->where('category', $request->input('category'));
        }
        if ($request->has('subcategory')) {
            $query->where('subcategory', $request->input('subcategory'));
        }
        if ($request->has('location')) {
            $query->where('location', 'like', '%' . $request->input('location') . '%');
        }
        if ($request->has('min_price')) {
            $query->where('price', '>=', $request->input('min_price'));
        }
        if ($request->has('max_price')) {
            $query->where('price', '<=', $request->input('max_price'));
        }
        if ($request->has('is_mobile')) {
            $query->where('is_mobile', $request->boolean('is_mobile'));
        }
        if ($request->has('search')) {
            $searchTerm = $request->input('search');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', '%' . $searchTerm . '%')
                    ->orWhere('description', 'like', '%' . $searchTerm . '%')
                    ->orWhere('category', 'like', '%' . $searchTerm . '%')
                    ->orWhere('subcategory', 'like', '%' . $searchTerm . '%');
            });
        }

        // Eager load user for seller details
        $services = $query->with('user')->paginate(10); // Paginate results

        return response()->json($services, 200);
    }

    /**
     * Display the specified service.
     * Publicly accessible, but only if active.
     */
    public function show(Service $service)
    {
        // The policy `view` method handles `is_active` check.
        return response()->json($service->load('user'), 200); // Eager load user
    }

    /**
     * Store a newly created service in storage.
     * Only for authenticated sellers. Policy handles limits.
     */
    public function store(StoreServiceRequest $request) // Use Form Request
    {
        // Policy 'create' method has already authorized based on seller status and package limits.
        $user = $request->user();

        $serviceData = $request->validated();
        $serviceData['user_id'] = $user->id;
        $serviceData['is_active'] = true; // New services are active by default, admin can moderate

        // Handle media files if present
        $mediaPaths = [];
        if ($request->hasFile('media_files')) {
            foreach ($request->file('media_files') as $file) {
                if ($file->isValid()) {
                    $path = $file->store('service_media', 'public'); // Store in 'public' disk
                    $mediaPaths[] = Storage::url($path); // Get public URL
                }
            }
        }
        $serviceData['media_files'] = $mediaPaths;

        $service = Service::create($serviceData);

        return response()->json([
            'message' => 'Service created successfully.',
            'service' => $service,
        ], 201);
    }

    /**
     * Update the specified service in storage.
     * Only for service owner.
     */
    public function update(UpdateServiceRequest $request, Service $service) // Use Form Request
    {
        // Policy 'update' method has already authorized based on ownership.
        $service->update($request->validated());

        return response()->json([
            'message' => 'Service updated successfully.',
            'service' => $service,
        ], 200);
    }

    /**
     * Remove the specified service from storage.
     * Only for service owner.
     */
    public function destroy(Service $service)
    {
        // Policy 'delete' method has already authorized based on ownership.

        // Delete associated media files
        if (is_array($service->media_files)) {
            foreach ($service->media_files as $url) {
                $path = str_replace('/storage/', '', $url); // Convert URL back to path
                if (Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                }
            }
        }

        $service->delete();

        return response()->json(['message' => 'Service deleted successfully.'], 200);
    }

    /**
     * Upload additional media files to an existing service.
     */
    public function uploadMedia(UploadServiceMediaRequest $request, Service $service)
    {
        // Authorization: Ensure user owns the service. Policy `update` handles this implicitly.
        $this->authorize('update', $service);

        $mediaPaths = $service->media_files ?? []; // Get existing media

        if ($request->hasFile('media_files')) {
            foreach ($request->file('media_files') as $file) {
                if ($file->isValid()) {
                    $path = $file->store('service_media', 'public');
                    $mediaPaths[] = Storage::url($path);
                }
            }
        }

        $service->update(['media_files' => $mediaPaths]);

        return response()->json([
            'message' => 'Media files uploaded successfully.',
            'service' => $service,
        ], 200);
    }

    // You might add a method to delete specific media files from a service too
    // public function deleteMedia(Request $request, Service $service, string $mediaUrl) { /* ... */ }
}