<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\User\UpdateProfileRequest; // Import
use App\Http\Requests\User\UploadProfileImageRequest; // Import
use App\Http\Requests\User\ChangePasswordRequest;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\User\DeleteAccountRequest;

class UserController extends Controller
{
    public function show(Request $request)
    {
        return response()->json(['user' => $request->user()]); // Returns the authenticated user object
    }

    public function update(UpdateProfileRequest $request) // Use Form Request
    {
        $user = $request->user();

        // Store original email and phone to check for changes
        $originalEmail = $user->email;
        $originalPhone = $user->phone_number;

        $user->fill($request->only([
            'name', 'email', 'phone_number', 'location', 'bio'
        ]));

        if ($user->isDirty('email') && $originalEmail !== $user->email) {
            $user->email_verified_at = null; // Mark email as unverified if changed
        }
        if ($user->isDirty('phone_number') && $originalPhone !== $user->phone_number) {
            $user->phone_verified_at = null; // Mark phone as unverified if changed
        }

        $user->save();

        return response()->json([
            'message' => 'Profile updated successfully.',
            'user' => $user->fresh(), // Return the freshly updated user object
        ], 200);
    }

    public function uploadProfileImage(UploadProfileImageRequest $request) // Use Form Request
    {
        $user = $request->user();

        // Delete old profile image if it exists and is stored publicly
        if ($user->profile_image && Storage::disk('public')->exists(str_replace('/storage/', '', $user->profile_image))) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $user->profile_image));
        }

        // Store new image in 'public' disk (symlinked to public/storage)
        $path = $request->file('profile_image')->store('profile_images', 'public');
        $user->profile_image = Storage::url($path); // Get public URL

        $user->save();

        return response()->json([
            'message' => 'Profile image uploaded successfully.',
            'profile_image_url' => $user->profile_image,
        ], 200);
    }
       /**
     * Change the authenticated user's password.
     */
    public function changePassword(ChangePasswordRequest $request)
    {
        $user = $request->user();
        $user->password = Hash::make($request->new_password);
        $user->save();

        // Revoke current token for security (optional but recommended after password change)
        // If using Sanctum API tokens primarily:
        $request->user()->tokens()->where('id', $request->user()->currentAccessToken()->id)->delete();

        return response()->json([
            'message' => 'Password updated successfully. Please log in with your new password.',
        ], 200);
    }
    public function deleteAccount(DeleteAccountRequest $request)
    {
        $user = $request->user();

        // Implement a soft delete by setting is_active to false.
        // You might also want to set user_type to 'inactive' or similar.
        $user->is_active = false;
        // Optionally, revoke all tokens to force logout from all devices
        $user->tokens()->delete();
        $user->save();

        // If you had soft deletes set up in the User model, it would be: $user->delete();

        return response()->json([
            'message' => 'Your account has been successfully deactivated.',
        ], 200);
    }
}