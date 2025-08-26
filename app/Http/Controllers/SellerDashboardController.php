<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Service;
use App\Models\Appointment;
use App\Models\Payment; // Still useful if we ever need to cross-check payments directly
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class SellerDashboardController extends Controller
{
    /**
     * Get aggregated insights for the authenticated seller's dashboard.
     */
    public function insights(Request $request)
    {
        $seller = $request->user();

        if (!$seller || !$seller->isSeller()) {
            return response()->json(['message' => 'Unauthorized. Only sellers can access this dashboard data.'], 403);
        }

        // --- Aggregated Data Points ---

        // 1. Active Services Count
        $activeServicesCount = Service::where('user_id', $seller->id)
                                    ->where('is_active', true)
                                    ->count();

        // 2. Pending Appointments Count
        $pendingAppointmentsCount = Appointment::where('seller_id', $seller->id)
                                            ->where('status', 'pending')
                                            ->count();

        // --- NEW: 3. Completed Appointments Count ---
        $completedAppointmentsCount = Appointment::where('seller_id', $seller->id)
                                                ->where('status', 'completed')
                                                ->count();

        // 4. Total Earnings (last 30 days) - based on completed appointments
        $totalEarningsLast30Days = Appointment::where('seller_id', $seller->id)
                                            ->where('status', 'completed')
                                            ->where('appointment_date', '>=', Carbon::now()->subDays(30))
                                            ->sum('total_amount');
        
        // --- NEW: 5. Total Revenue (All-Time Earnings) ---
        $allTimeEarnings = Appointment::where('seller_id', $seller->id)
                                    ->where('status', 'completed')
                                    ->sum('total_amount');

        // 6. Monthly Earnings Trend (last 6 months, for chart if needed)
        $monthlyEarningsTrend = Appointment::select(
                DB::raw('DATE_FORMAT(appointment_date, "%Y-%m") as month'),
                DB::raw('SUM(total_amount) as total_amount')
            )
            ->where('seller_id', $seller->id)
            ->where('status', 'completed')
            ->where('appointment_date', '>=', Carbon::now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return response()->json([
            'message' => 'Seller dashboard insights retrieved successfully.',
            'data' => [
                'active_services_count' => $activeServicesCount,
                'pending_appointments_count' => $pendingAppointmentsCount,
                'completed_appointments_count' => $completedAppointmentsCount, // NEW
                'total_earnings_last_30_days' => round($totalEarningsLast30Days, 2),
                'all_time_earnings' => round($allTimeEarnings, 2),           // NEW
                'monthly_earnings_trend' => $monthlyEarningsTrend,
            ]
        ]);
    }
}