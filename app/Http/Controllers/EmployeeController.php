<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Storage;

class EmployeeController extends Controller
{
    public function index()
    {
        $user = User::with('center', 'roles')
            ->where('id', auth()->id())
            ->first();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json($user);
    }

   public function update(Request $request)
{
    $user = $request->user();

    $request->validate([
        'first_name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'email' => 'required|email',
        'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        'new_sign_file' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        'keyboard_settings' => 'nullable|string',
    ]);

    if ($request->hasFile('photo')) {

        // Delete old image
        if ($user->image && Storage::exists('public/'.$user->image)) {
            Storage::delete('public/'.$user->image);
        }

        $path = $request->file('photo')->store('profile', 'public');
        $user->image = $path;
    }

    if ($request->hasFile('new_sign_file')) {
        //Do not delete old signature that might be used in other applications
        $path = $request->file('new_sign_file')->store('sign_files', 'public');
        $user->sign_file = $path;
    }

    $user->first_name = $request->first_name;
    $user->last_name = $request->last_name;
    $user->email = $request->email;
    $user->center_id = $request->center_id;
    if ($request->has('keyboard_settings')) {

    $settings = $request->keyboard_settings;

    // if string comes from frontend
    if (is_string($settings)) {
        $settings = json_decode($settings, true);
    }

    // safety cleanup
    if (!is_array($settings)) {
        $settings = [];
    }

    $user->keyboard_settings = $settings;
}    $user->save();

   if ($request->role_id) {
    $user->roles()->sync([$request->role_id]);
}

    return response()->json(['message' => 'Profile updated successfully']);
}

public function changePassword(Request $request)
    {
        $user = $request->user();

        // Validate request
        $request->validate([
            'old_password' => 'required|string',
            'new_password' => 'required|string|min:6|confirmed', // confirmed uses new_password_confirmation
        ]);

        // Check old password
        if (!Hash::check($request->old_password, $user->password)) {
            return response()->json([
                'message' => 'Old password is incorrect'
            ], 422);
        }

        // Update password
        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json([
            'message' => 'Password updated successfully'
        ]);
    }
}
