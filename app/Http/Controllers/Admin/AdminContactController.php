<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\Contact\RespondToContactRequest; // Import

class AdminContactController extends Controller
{
    /**
     * Display a listing of contact messages for admin.
     */
    public function index(Request $request)
    {
        $query = Contact::query();

        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }
        if ($request->has('search')) {
            $searchTerm = $request->input('search');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%')
                    ->orWhere('email', 'like', '%' . $searchTerm . '%')
                    ->orWhere('subject', 'like', '%' . $searchTerm . '%')
                    ->orWhere('message', 'like', '%' . $searchTerm . '%');
            });
        }

        $contacts = $query->latest()->paginate(15);

        return response()->json($contacts, 200);
    }

    /**
     * Display the specified contact message.
     */
    public function show(Contact $contact)
    {
        if ($contact->status === 'unread') {
            $contact->status = 'read'; // Mark as read when viewed
            $contact->save();
        }
        return response()->json($contact, 200);
    }

    /**
     * Respond to a contact message.
     */
    public function respond(RespondToContactRequest $request, Contact $contact) // Use Form Request
    {
        $contact->admin_response = $request->input('admin_response');
        $contact->status = 'responded';
        $contact->save();

        // TODO: Send an email notification to the user who sent the contact message
        // This would involve a new Notification class.

        return response()->json([
            'message' => 'Response sent and contact marked as responded.',
            'contact' => $contact,
        ], 200);
    }
}