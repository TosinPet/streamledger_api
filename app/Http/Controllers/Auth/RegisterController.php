<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    //
    public function register(Request $request)
    {
        try {
            // Validate the request data
            $validated = $request->validate([
                'first_name' => 'required|max:255',
                'last_name' => 'required|max:255',
                'email' => 'required|max:225|email|unique:users,email',
                'phone' => 'bail|required|string|unique:users,phone',
                'password' => 'bail|required|integer|min:6|confirmed',
                
            ]);

            $password = Hash::make($validated['password']);
            $ref = 'USER'.random_int(100000, 999999);

            // Create the user
            $user = User::create([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'ref' => $ref,
                'password' => $password,
            ]);

            // Return the response with the created user and token
            return response()->json([
                'user' => $user,
                'message' => 'Your account has been cretaed, you can now login',
            ], 201);

        } catch (\Exception $e) {
            // Return error response in case of an exception
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
