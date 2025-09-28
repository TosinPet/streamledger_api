<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    //
    public function show(Request $request)
    {
        try{

            $wallet = $request->user()->wallet;

            if (!$wallet) {
                $wallet = $request->user()->wallet()->create(['balance' => 0]);
            }
        
            return response()->json([
                'user' => $request->user()->only(['id', 'first_name', 'last_name', 'email']),
                'balance' => $wallet->balance,
            ], 200);


        } catch (\Exception $e)
        {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);

        }
    }
}
