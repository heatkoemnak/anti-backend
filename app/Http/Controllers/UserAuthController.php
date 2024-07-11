<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserAuthController extends Controller
{
    public function get_all_users()
    {
        $users = User::get();
        return response()->json($users, 200);
    }

    public function register(Request $request)
    {
        $registerUserData = $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|min:8',
            'role' => 'required|string|in:user,admin',
        ]);

        // not allow user to register with same credential as admin

        if ($registerUserData['email'] === env('ADMIN_EMAIL')) {
            return response()->json(['message' => 'Cannot register with this email'], 403);
        }
        $user = User::create([
            'name' => $registerUserData['name'],
            'email' => $registerUserData['email'],
            'password' => Hash::make($registerUserData['password']),
            'role' => $registerUserData['role'],
        ]);
        return response()->json([
            'message' => 'User Created ',
        ]);
    }

    public function login(Request $request)
    {
        $loginUserData = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|min:8'
        ]);

        //log admin in
        if ($loginUserData['email'] === env('ADMIN_EMAIL')) {
            if ($loginUserData['password'] !== env('ADMIN_PASSWORD')) {
                return response()->json([
                    'message' => 'Invalid Credentials',
                ], 401);
            }

            // Fetch the admin user
            $user = User::where('email', $loginUserData['email'])->first();
            return response('Welcome Admin!');
        } else {
            // Fetch the regular user
            $user = User::where('email', $loginUserData['email'])->first();

            if (!$user || !Hash::check($loginUserData['password'], $user->password)) {
                return response()->json([
                    'message' => 'Invalid Credentials',
                ], 401);
            }
        }
        $token = $user->createToken($user->name . '-AuthToken')->plainTextToken;
        return response()->json([
            'token' => $token,
        ]);
    }
    public function logout()
    {
        auth()->user()->tokens()->delete();
        return response()->json([
            "message" => "logged out"
        ]);
    }
    public function user(Request $request)
    {
        return response()->json($request->user());
    }
    public function show($id)
    {
        $user = User::findOrFail($id);

        return response()->json($user, 200);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Prevent updates to admin's email and password
        if ($user->email === env('ADMIN_EMAIL')) {
            return response()->json(['message' => 'Admin credentials cannot be updated'], 403);
        }

        $updateUserData = $request->validate([
            'name' => 'sometimes|required|string',
            'email' => 'sometimes|required|string|email|unique:users,email,' . $id,
            'password' => 'sometimes|required|min:8',
            'role' => 'sometimes|required|string|in:user,admin',
        ]);

        if (isset($updateUserData['password'])) {
            $updateUserData['password'] = Hash::make($updateUserData['password']);
        }

        $user->update($updateUserData);

        return response()->json(['message' => 'User Updated'], 200);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(['message' => 'User Deleted'], 200);
    }
}
