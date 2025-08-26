<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Appointment;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class CustomerDashboardController extends Controller
{
    /**
     * Get aggregated insights for the authenticated customer's dashboard.
     */
    public function insights(Request $request)
    {
        $customer = $request->user(); // Get the currently authenticated customer

        // Ensure the authenticated user is actually a customer
        if (!$customer || !$customer->isCustomer()) {
            return response()->json(['message' => 'Unauthorized. Only customers can access this dashboard data.'], 403);
        }

        // --- Aggregated Data Points ---

        // 1. Total Appointments Booked (all time)
        $totalAppointmentsCount = Appointment::where('customer_id', $customer->id)->count();

        // 2. Upcoming Appointments Count (pending or confirmed, in the future)
        $upcomingAppointmentsCount = Appointment::where('customer_id', $customer->id)
                                                ->whereIn('status', ['pending', 'confirmed'])
                                                ->where('appointment_date', '>=', now())
                                                ->count();

        // 3. Completed Appointments Count
        $completedAppointmentsCount = Appointment::where('customer_id', $customer->id)
                                                 ->where('status', 'completed')
                                                 ->count();

        // 4. Cancelled Appointments Count
        $cancelledAppointmentsCount = Appointment::where('customer_id', $customer->id)
                                                 ->where('status', 'cancelled')
                                                 ->count();

        // 5. Total Amount Spent (sum of completed payments made by this customer)
        $totalAmountSpent = Payment::where('user_id', $customer->id) // 'user_id' in Payments is the one *making* the payment
                                    ->where('status', 'completed')
                                    ->sum('amount');

        // 6. Recent Appointments (e.g., last 5, for a quick overview on the dashboard)
        $recentAppointments = Appointment::where('customer_id', $customer->id)
                                        ->with(['service.user', 'seller']) // Eager load for display details
                                        ->latest('appointment_date') // Order by most recent
                                        ->limit(5) // Limit to 5 for dashboard summary
                                        ->get();

        return response()->json([
            'message' => 'Customer dashboard insights retrieved successfully.',
            'data' => [
                'total_appointments_count' => $totalAppointmentsCount,
                'upcoming_appointments_count' => $upcomingAppointmentsCount,
                'completed_appointments_count' => $completedAppointmentsCount,
                'cancelled_appointments_count' => $cancelledAppointmentsCount,
                'total_amount_spent' => round($totalAmountSpent, 2), // Round to 2 decimal places
                'recent_appointments' => $recentAppointments,
            ]
        ]);
    }
}