<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;

class AdminServiceController extends Controller
{
    /**
     * Display a listing of all services for admin.
     */
    public function index(Request $request)
    {
        $query = Service::query()->with('user'); // Eager load service owner

        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }
        if ($request->has('category')) {
            $query->where('category', $request->input('category'));
        }
        if ($request->has('seller_id')) {
            $query->where('user_id', $request->input('seller_id'));
        }
        if ($request->has('search')) {
            $searchTerm = $request->input('search');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', '%' . $searchTerm . '%')
                    ->orWhere('description', 'like', '%' . $searchTerm . '%');
            });
        }

        $services = $query->paginate(15);

        return response()->json($services, 200);
    }

    /**
     * Display the specified service.
     */
    public function show(Service $service)
    {
        return response()->json($service->load('user'), 200);
    }

    /**
     * Update the specified service's active status (admin moderation).
     */
    public function update(Request $request, Service $service)
    {
        $request->validate([
            'is_active' => ['required', 'boolean'],
        ]);

        $service->is_active = $request->boolean('is_active');
        $service->save();

        return response()->json([
            'message' => 'Service status updated successfully.',
            'service' => $service,
        ], 200);
    }

    // Admin does not store or destroy services directly. Sellers store, and services are rarely deleted, usually just deactivated.
}