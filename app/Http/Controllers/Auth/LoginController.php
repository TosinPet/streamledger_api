<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    //
    public function login(Request $request)
    {
        try{

            $validated = $request->validate([
                'email' => 'required',
                'password' => 'required|min:6'
            ]);
    
            if (! Auth::attempt($validated)){
                return response()->json([
                    'message' => 'Login information is invalid',    
                ], 401);
            }
    
            $user = User::where('email', $validated['email'])->first();

            $user = Auth::user();

            return response()->json([
                'access_token' => $user->createToken('api_token')->plainTextToken,
                'token_type' => 'Bearer',
                'message' => 'You have been logged in',
            ], 200);


        } catch (\Exception $e)
        {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);

        }
    }
}
