<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Appointment;
use Illuminate\Auth\Access\Response;
use Illuminate\Http\Request;

class AppointmentPolicy
{
    // <<<--- 2. INJECT Request VIA CONSTRUCTOR
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Allow admins to perform any action.
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->isAdmin()) {
            return true;
        }
        return null;
    }

    /**
     * Determine whether the user can view any appointments.
     * Users can only view their own appointments (customer or seller).
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the appointment.
     * User must be the customer or the seller of the appointment.
     */
    public function view(User $user, Appointment $appointment): Response
    {
        return ($user->id === $appointment->customer_id || $user->id === $appointment->seller_id)
            ? Response::allow()
            : Response::deny('You are not authorized to view this appointment.');
    }

    /**
     * Determine whether the user can create appointments.
     * Only customers can book appointments.
     */
    public function create(User $user): Response
    {
        return $user->isCustomer()
            ? Response::allow()
            : Response::deny('Only customers can book appointments.');
    }

    /**
     * Determine whether the user can update the appointment.
     * Customer can cancel. Seller can confirm, complete, cancel.
     */
    public function update(User $user, Appointment $appointment): Response
    {
        if ($user->id === $appointment->customer_id) {
            // Customer can only cancel or update notes (if notes is separate from status)
            // If the request tries to change status to anything other than 'cancelled'
            if ($this->request->has('status') && $this->request->input('status') !== 'cancelled') {
                return Response::deny('As a customer, you can only cancel your appointments.');
            }
            // Allow if just notes or valid cancellation attempt
            return Response::allow();
        }

        if ($user->id === $appointment->seller_id) {
            // Seller can update status (confirm, complete, cancel) and notes
            return Response::allow();
        }

        return Response::deny('You are not authorized to update this appointment.');
    }

    /**
     * Determine whether the user can delete the appointment.
     * Generally, appointments are cancelled, not deleted. Maybe only admin can delete.
     */
    public function delete(User $user, Appointment $appointment): Response
    {
        return Response::deny('Appointments cannot be deleted directly. Please cancel them.');
    }
}