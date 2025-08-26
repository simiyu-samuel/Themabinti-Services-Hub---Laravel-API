<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Service;
use App\Models\Appointment;
use App\Models\Payment;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    /**
     * Get aggregated dashboard data.
     */
    public function index()
    {
        $totalUsers = User::count();
        $totalCustomers = User::where('user_type', 'customer')->count();
        $totalSellers = User::where('user_type', 'seller')->count();
        $activeSellers = User::where('user_type', 'seller')->where('is_active', true)->whereNotNull('seller_package')->where('package_expiry_date', '>', now())->count();

        $totalServices = Service::count();
        $activeServices = Service::where('is_active', true)->count();
        $pendingServices = Service::where('is_active', false)->count(); // Services that might need admin approval

        $totalAppointments = Appointment::count();
        $pendingAppointments = Appointment::where('status', 'pending')->count();
        $completedAppointments = Appointment::where('status', 'completed')->count();

        $totalRevenue = Payment::where('status', 'completed')->sum('amount');
        $monthlyRevenue = Payment::where('status', 'completed')
                                ->whereMonth('created_at', now()->month)
                                ->whereYear('created_at', now()->year)
                                ->sum('amount');

        $unreadContacts = Contact::where('status', 'unread')->count();

        // Example: Revenue trend over last 6 months
        $revenueTrend = Payment::select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('SUM(amount) as total_amount')
            )
            ->where('status', 'completed')
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Example: Appointments by status
        $appointmentStatusBreakdown = Appointment::select('status', DB::raw('count(*) as count'))
                                                ->groupBy('status')
                                                ->get();

        return response()->json([
            'stats' => [
                'total_users' => $totalUsers,
                'total_customers' => $totalCustomers,
                'total_sellers' => $totalSellers,
                'active_sellers' => $activeSellers,
                'total_services' => $totalServices,
                'active_services' => $activeServices,
                'pending_services' => $pendingServices,
                'total_appointments' => $totalAppointments,
                'pending_appointments' => $pendingAppointments,
                'completed_appointments' => $completedAppointments,
                'total_revenue' => $totalRevenue,
                'monthly_revenue' => $monthlyRevenue,
                'unread_contacts' => $unreadContacts,
            ],
            'charts' => [
                'revenue_trend_last_6_months' => $revenueTrend,
                'appointment_status_breakdown' => $appointmentStatusBreakdown,
            ],
            'message' => 'Admin dashboard data retrieved successfully.',
        ], 200);
    }
}