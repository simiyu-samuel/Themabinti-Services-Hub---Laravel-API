<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\User\UpdateUserStatusRequest; // Used for is_active toggle
use App\Http\Requests\Admin\User\AdminDeleteUserRequest; // NEW: For admin deletion, to ensure it's not 
use Illuminate\Support\Facades\Log; // <<< ADD THIS IMPORT
use Illuminate\Support\Facades\DB;  // <<< ADD THIS IMPORT FOR QUERY LOGGING

class AdminUserController extends Controller
{
    /**
     * Display a listing of users with filters.
     */
    // public function index(Request $request)
    // {
    //     $query = User::withTrashed();

    //     if ($request->has('user_type')) {
    //         $query->where('user_type', $request->input('user_type'));
    //     }
    //     if ($request->has('is_active')) {
    //         $query->where('is_active', $request->boolean('is_active'));
    //     }
    //     if ($request->has('seller_package')) {
    //         $query->where('seller_package', $request->input('seller_package'));
    //     }
    //     if ($request->has('search')) {
    //         $searchTerm = $request->input('search');
    //         $query->where(function ($q) use ($searchTerm) {
    //             $q->where('name', 'like', '%' . $searchTerm . '%')
    //                 ->orWhere('email', 'like', '%' . $searchTerm . '%')
    //                 ->orWhere('phone_number', 'like', '%' . $searchTerm . '%');
    //         });
    //     }
        
    //     if ($request->boolean('trashed')) { // Filter for soft-deleted (trashed) users
    //         $query->onlyTrashed();
    //     }

    //     $users = $query->paginate(15);
        
    //     \Log::info('User data: ', $users->toArray());

    //     return response()->json($users, 200);
    // }
public function index(Request $request)
    {
        Log::info('AdminUserController@index called.');
        Log::info('Request Query Params: ' . json_encode($request->query()));

        DB::enableQueryLog();

        $query = User::withTrashed(); // Include soft-deleted users by default in admin view

        // --- IMPORTANT FIXES FOR FILTER LOGIC ---

        // Filter by user_type: only apply if a non-empty string is provided
        if ($request->filled('user_type')) { // Use filled() to check for non-empty string
            $userType = $request->input('user_type');
            Log::info('Filtering by user_type: ' . $userType);
            $query->where('user_type', $userType);
        }

        // Filter by is_active: check if the parameter is actually present and not an empty string
        // request()->has() is fine here, but then convert to boolean properly.
        // It's often safer to check for specific string values 'true'/'false' or explicit booleans.
        // If frontend sends 'is_active: null' it becomes 'false'.
        // If frontend sends 'is_active: ""' it becomes 'false'.
        // Let's make it only apply the filter if it's explicitly 'true' or 'false'.
        if ($request->has('is_active') && in_array($request->input('is_active'), ['true', 'false', true, false], true)) {
            $isActive = $request->boolean('is_active');
            Log::info('Filtering by is_active: ' . ($isActive ? 'true' : 'false'));
            $query->where('is_active', $isActive);
        }
        // If the 'is_active' filter is NOT provided, it means show both active and inactive.

        if ($request->filled('seller_package')) { // Use filled()
            $sellerPackage = $request->input('seller_package');
            Log::info('Filtering by seller_package: ' . $sellerPackage);
            $query->where('seller_package', $sellerPackage);
        }

        if ($request->filled('search')) { // Use filled()
            $searchTerm = $request->input('search');
            Log::info('Filtering by search term: ' . $searchTerm);
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('email', 'like', '%' . $searchTerm . '%')
                  ->orWhere('phone_number', 'like', '%' . $searchTerm . '%');
            });
        }
        
        // Filter by trashed: only apply if explicitly true
        if ($request->boolean('trashed')) {
            Log::info('Filtering for only trashed users.');
            $query->onlyTrashed();
        } else {
            // Default: if 'trashed' is not true, only show non-trashed (undeleted) users
            // unless specific filters require showing all (e.g. searching for a deleted user by name)
            // For admin's default view, usually exclude trashed unless explicitly asked.
            $query->whereNull('deleted_at'); // Explicitly only show non-trashed by default
        }
        // --- END IMPORTANT FIXES ---

        $users = $query->paginate(15);

        Log::info('Executed User Query: ' . json_encode(DB::getQueryLog()));
        Log::info('Paginated Users Data Total: ' . $users->total());

        return response()->json($users, 200);
    }

    /**
     * Display the specified user.
     */
    public function show(User $user)
    {
        return response()->json($user, 200);
    }

    /**
     * Update the specified user's status (active/inactive).
     * Admins cannot change their own user_type or deactivate themselves.
     */
    public function update(UpdateUserStatusRequest $request, User $user) // Use Form Request
    {
        if ($user->id === $request->user()->id && !$request->boolean('is_active')) {
            return response()->json(['message' => 'You cannot deactivate your own admin account.'], 403);
        }

        $user->is_active = $request->boolean('is_active');
        $user->save();

        return response()->json([
            'message' => 'User status updated successfully.',
            'user' => $user,
        ], 200);
    }

    /**
     * Remove the specified user from storage.
     * Soft deletes are preferred, or just deactivate.
     */
    public function destroy(AdminDeleteUserRequest $request, User $user)
    {
        // The AdminDeleteUserRequest already validates that the admin is not deleting themselves
        // and has provided their own password.

        $user->delete(); // This performs a soft delete due to SoftDeletes trait
        // Optionally, revoke all tokens of the deleted user to force logout from all devices
        $user->tokens()->delete();

        return response()->json(['message' => 'User account successfully marked as deleted (deactivated).'], 200);
    }
        /**
     * Restore a soft-deleted user.
     */
    public function restore(Request $request, int $id)
    {
        // Authenticated admin should be checked by middleware before this method is called
        $user = User::onlyTrashed()->findOrFail($id); // Find only soft-deleted users
        $user->restore(); // Restore the user (sets deleted_at to null)
        $user->is_active = true; // Optionally reactivate upon restore

        return response()->json([
            'message' => 'User account successfully restored and reactivated.',
            'user' => $user->fresh(),
        ], 200);
    }
     /**
     * Permanently delete a user from the database.
     * This is a dangerous operation, use with extreme caution.
     */
    public function forceDelete(AdminDeleteUserRequest $request, int $id)
    {
        // The AdminDeleteUserRequest already validates the admin's password and ensures not self-deleting.
        $user = User::onlyTrashed()->findOrFail($id); // Find only soft-deleted users for permanent deletion
        $user->forceDelete(); // This performs a permanent delete

        return response()->json(['message' => 'User account permanently deleted from the system.'], 200);
    }
}