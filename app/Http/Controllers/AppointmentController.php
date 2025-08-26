<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Service;
use Illuminate\Http\Request;
use App\Http\Requests\Appointment\StoreAppointmentRequest; // Import
use App\Http\Requests\Appointment\UpdateAppointmentRequest; // Import
use Illuminate\Auth\Access\AuthorizationException;
use App\Notifications\NewAppointmentNotification; // Will create
use App\Notifications\AppointmentStatusChangedNotification; // Will create

class AppointmentController extends Controller
{
    public function __construct()
    {
        // Apply policies automatically for resource methods
        $this->authorizeResource(Appointment::class, 'appointment');
    }

    /**
     * Display a listing of appointments for the authenticated user (customer or seller).
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $appointments = Appointment::query()
            ->where(function ($query) use ($user) {
                $query->where('customer_id', $user->id)
                        ->orWhere('seller_id', $user->id);
            })
            ->with(['service.user', 'customer', 'seller']) // Eager load relationships
            ->latest('appointment_date')
            ->paginate(10);

        return response()->json($appointments, 200);
    }

    /**
     * Store a newly created appointment in storage.
     * Only customers can book.
     */
    public function store(StoreAppointmentRequest $request) // Use Form Request
    {
        // Policy 'create' method has already authorized.
        $customer = $request->user();
        $service = Service::findOrFail($request->service_id);

        $appointment = Appointment::create([
            'customer_id' => $customer->id,
            'seller_id' => $service->user_id,
            'service_id' => $service->id,
            'appointment_date' => $request->appointment_date,
            'status' => 'pending', // All new appointments are pending initially
            'total_amount' => $service->price, // Calculate based on service price
            'notes' => $request->notes,
        ]);

        // Notify seller about new appointment (via queue)
        $service->user->notify(new NewAppointmentNotification($appointment));
        // Notify customer about successful booking (via queue)
        $customer->notify(new NewAppointmentNotification($appointment));

        return response()->json([
            'message' => 'Appointment booked successfully. Awaiting seller confirmation.',
            'appointment' => $appointment->load(['service', 'customer', 'seller']),
        ], 201);
    }

    /**
     * Display the specified appointment.
     * Only customer or seller can view their appointments.
     */
    public function show(Appointment $appointment)
    {
        // Policy 'view' method has already authorized.
        return response()->json($appointment->load(['service', 'customer', 'seller']), 200);
    }

    /**
     * Update the specified appointment in storage.
     * Customer can cancel. Seller can confirm, complete, cancel.
     */
    public function update(UpdateAppointmentRequest $request, Appointment $appointment) // Use Form Request
    {
        // Policy 'update' method has already authorized.

        $oldStatus = $appointment->status;
        $appointment->update($request->validated());
        $newStatus = $appointment->status;

        // Send notification if status changed
        if ($oldStatus !== $newStatus) {
            // Notify customer if seller changed status
            if ($request->user()->id === $appointment->seller_id) {
                $appointment->customer->notify(new AppointmentStatusChangedNotification($appointment, $oldStatus, $newStatus));
            }
            // Notify seller if customer cancelled (only case customer updates status)
            if ($request->user()->id === $appointment->customer_id && $newStatus === 'cancelled') {
                $appointment->seller->notify(new AppointmentStatusChangedNotification($appointment, $oldStatus, $newStatus));
            }
        }

        return response()->json([
            'message' => 'Appointment updated successfully.',
            'appointment' => $appointment->load(['service', 'customer', 'seller']),
        ], 200);
    }

    /**
     * Remove the specified appointment from storage.
     * Typically, appointments are cancelled, not deleted.
     */
    public function destroy(Appointment $appointment)
    {
        // Policy 'delete' method has already authorized (likely denies).
        return response()->json(['message' => 'Appointments cannot be deleted directly. Please cancel them.'], 403);
    }
}