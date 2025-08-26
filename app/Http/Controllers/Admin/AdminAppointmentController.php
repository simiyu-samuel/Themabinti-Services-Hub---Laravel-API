<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Http\Request;

class AdminAppointmentController extends Controller
{
    /**
     * Display a listing of all appointments for admin.
     */
    public function index(Request $request)
    {
        $query = Appointment::query()->with(['customer', 'seller', 'service']); // Eager load related data

        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }
        if ($request->has('customer_id')) {
            $query->where('customer_id', $request->input('customer_id'));
        }
        if ($request->has('seller_id')) {
            $query->where('seller_id', $request->input('seller_id'));
        }
        if ($request->has('service_id')) {
            $query->where('service_id', $request->input('service_id'));
        }
        if ($request->has('date')) { // Filter by specific date
            $query->whereDate('appointment_date', $request->input('date'));
        }

        $appointments = $query->latest('appointment_date')->paginate(15);

        return response()->json($appointments, 200);
    }

    /**
     * Display the specified appointment.
     */
    public function show(Appointment $appointment)
    {
        return response()->json($appointment->load(['customer', 'seller', 'service']), 200);
    }

    /**
     * Update the status of the specified appointment (admin can override).
     */
    public function update(Request $request, Appointment $appointment)
    {
        $request->validate([
            'status' => ['required', 'string', 'in:pending,confirmed,completed,cancelled'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $oldStatus = $appointment->status;
        $appointment->status = $request->input('status');
        $appointment->notes = $request->input('notes', $appointment->notes); // Admin can update notes
        $appointment->save();

        // Consider dispatching notifications to involved parties if status changes
        if ($oldStatus !== $appointment->status) {
            // Notifying both customer and seller about admin status change
            $appointment->customer->notify(new \App\Notifications\AppointmentStatusChangedNotification($appointment, $oldStatus, $appointment->status));
            $appointment->seller->notify(new \App\Notifications\AppointmentStatusChangedNotification($appointment, $oldStatus, $appointment->status));
        }

        return response()->json([
            'message' => 'Appointment updated by admin successfully.',
            'appointment' => $appointment->load(['customer', 'seller', 'service']),
        ], 200);
    }

    // Admin does not create or destroy appointments directly.
}