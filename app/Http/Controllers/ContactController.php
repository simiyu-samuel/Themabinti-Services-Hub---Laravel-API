<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;
use App\Http\Requests\Contact\StoreContactRequest; // Import
use App\Notifications\NewContactMessageNotification; // Will create

class ContactController extends Controller
{
    /**
     * Store a new contact message.
     */
    public function store(StoreContactRequest $request) // Use Form Request
    {
        $contact = Contact::create($request->validated());

        // Notify admins about the new contact message
        // Find all admins and send notification
        $admins = \App\Models\User::where('user_type', 'admin')->get();
        foreach ($admins as $admin) {
            $admin->notify(new NewContactMessageNotification($contact));
        }

        return response()->json([
            'message' => 'Your message has been received. We will get back to you soon.',
            'contact' => $contact,
        ], 201);
    }
}