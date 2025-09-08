<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AdminAuthController extends Controller
{
    public function register(Request $request)
    {
        // ✅ Validate inputs, including optional avatar image
        $data = $request->validate([
            'name'     => ['required','string','max:255'],
            'email'    => ['required','string','email','max:255','unique:admins,email'],
            'password' => ['required','string','min:6'],
            'avatar'   => ['nullable','image','mimes:jpg,jpeg,png','max:2048'], // 2MB
        ]);

        // ✅ Store avatar if provided
        $avatarPath = null;
        if ($request->hasFile('avatar')) {
            // Saves to storage/app/public/admin_avatars/xxxx.jpg
            $avatarPath = $request->file('avatar')->store('admin_avatars', 'public');
        }

        // ✅ Create admin
        $admin = Admin::create([
            'name'        => $data['name'],
            'email'       => $data['email'],
            'password'    => Hash::make($data['password']),
            'avatar_path' => $avatarPath, // make sure Admin model has this in $fillable
        ]);

        return response()->json([
            'message' => '✅ Admin registered successfully.',
            'admin'   => [
                'id'          => $admin->id,
                'name'        => $admin->name,
                'email'       => $admin->email,
                'avatar_path' => $admin->avatar_path,
                // Optional convenience URL if you’ve run `php artisan storage:link`
                'avatar_url'  => $admin->avatar_path ? asset('storage/'.$admin->avatar_path) : null,
            ],
        ], 201);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::guard('admin')->attempt($credentials)) {
            return response()->json(['message' => '✅ Admin login successful']);
        }

        return response()->json(['message' => 'Invalid credentials'], 401);
    }
}
