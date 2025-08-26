<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GeneralBooking;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\GeneralBooking\UpdateGeneralBookingRequest;

class AdminGeneralBookingController extends Controller
{
    /**
     * Display a listing of general booking requests for admin.
     */
    public function index(Request $request)
    {
        $query = GeneralBooking::query()->with('assignedSeller'); // Eager load assigned seller

        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }
        if ($request->has('search')) {
            $searchTerm = $request->input('search');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('customer_name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('customer_email', 'like', '%' . $searchTerm . '%')
                  ->orWhere('customer_phone', 'like', '%' . $searchTerm . '%')
                  ->orWhere('message', 'like', '%' . $searchTerm . '%');
            });
        }

        $bookings = $query->latest('created_at')->paginate(15);

        return response()->json($bookings, 200);
    }

    /**
     * Display the specified general booking request.
     */
    public function show(GeneralBooking $generalBooking)
    {
        return response()->json($generalBooking->load('assignedSeller'), 200);
    }

    /**
     * Update the specified general booking request (status, assignment, notes).
     */
    public function update(UpdateGeneralBookingRequest $request, GeneralBooking $generalBooking)
    {
        $generalBooking->update($request->validated());

        // Optionally notify customer/seller if status changed or assigned
        // if ($request->has('status') || $request->has('assigned_seller_id')) {
        //     $generalBooking->customer_email; // Can send email here
        //     // If assigned, $generalBooking->assignedSeller->notify(...)
        // }

        return response()->json([
            'message' => 'General booking request updated successfully.',
            'booking' => $generalBooking->load('assignedSeller'),
        ], 200);
    }

    /**
     * Delete a general booking request (e.g., if it's spam or resolved).
     */
    public function destroy(GeneralBooking $generalBooking)
    {
        $generalBooking->delete();
        return response()->json(['message' => 'General booking request deleted successfully.'], 200);
    }
}