<?php

namespace App\Http\Controllers;

use App\Models\GeneralBooking;
use Illuminate\Http\Request;
use App\Http\Requests\GeneralBooking\StoreGeneralBookingRequest;
use App\Notifications\NewGeneralBookingNotification; // NEW Notification
use App\Models\User; // To notify admins

class GeneralBookingController extends Controller
{
    /**
     * Store a newly created general booking request.
     */
    public function store(StoreGeneralBookingRequest $request)
    {
        $booking = GeneralBooking::create($request->validated());

        // Notify admins about the new general booking request
        $admins = User::where('user_type', 'admin')->get();
        foreach ($admins as $admin) {
            $admin->notify(new NewGeneralBookingNotification($booking));
        }

        return response()->json([
            'message' => 'Your general appointment request has been received. We will contact you soon!',
            'booking' => $booking,
        ], 201);
    }
}