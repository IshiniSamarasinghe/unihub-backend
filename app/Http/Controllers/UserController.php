<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    // Fetch all users with their roles
    public function index() {
        return User::with('roles')->get();
    }

    // Delete a user by ID
    public function destroy($id) {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $user->delete();

        return response()->json(['message' => 'User deleted successfully'], 200);
    }

    // ✏️ Update user details (phone, faculty, user_type)
    public function update(Request $request, $id) {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        // Validate optional fields
        $request->validate([
            'phone' => ['nullable', 'string'],
            'faculty' => ['nullable', 'string'],
            'user_type' => ['nullable', 'in:normal_user,super_user']
        ]);

        // Update user fields
        $user->update($request->only(['phone', 'faculty', 'user_type']));

        return response()->json(['message' => 'User updated successfully', 'user' => $user], 200);
    }
}
