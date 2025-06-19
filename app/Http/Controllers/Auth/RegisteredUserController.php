<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class RegisteredUserController extends Controller
{
    /**
     * Handle an incoming registration request.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            Log::info('🔍 Starting user registration process');

            // ✅ Step 1: Validate the input
            $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password' => ['required', 'string'],
                'phone' => ['required', 'string'],
                'faculty' => ['required', 'string'],
                'roles' => ['required', 'array'],
                'roles.*.society' => ['required', 'string'],
                'roles.*.role' => ['required', 'string'],
            ]);

            Log::info('✅ Validation passed');
            Log::info('🧾 Incoming roles:', $request->roles);

            // ✅ Step 2: Check for super user role ("coeditor")
            $isSuperUser = false;

            foreach ($request->roles as $r) {
                $normalizedRole = strtolower(str_replace([' ', '-', '_'], '', trim($r['role'])));
                Log::info('🔍 Normalized role: ' . $normalizedRole);

                if ($normalizedRole === 'coeditor') {
                    $isSuperUser = true;
                    break;
                }
            }

            Log::info('🧠 Super user status resolved: ' . ($isSuperUser ? 'super_user' : 'normal_user'));

            // ✅ Step 3: Create user
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
                'faculty' => $request->faculty,
                'user_type' => $isSuperUser ? 'super_user' : 'normal_user',
            ]);

            Log::info('✅ User created: ID ' . $user->id);

            // ✅ Step 4: Save roles
            foreach ($request->roles as $role) {
                $user->roles()->create([
                    'society' => $role['society'],
                    'role' => $role['role'],
                ]);
                Log::info('🎯 Role assigned: ' . json_encode($role));
            }

            // ✅ Step 5: Auto-login & fire registration event
            Auth::login($user);
            event(new Registered($user));

            return response()->json([
                'message' => 'User registered successfully'
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'error' => 'Validation error',
                'details' => $e->errors()
            ], 422);

        } catch (\Throwable $e) {
            Log::error('❌ Registration failed', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'error' => 'Registration error',
                'details' => $e->getMessage()
            ], 500);
        }
    }
}
