<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function addMoney(Request $request)
    {

        // Implement validation, authentication, and adding money logic here
        try {
            $validator = Validator::make($request->all(), [
                'amount' => 'required|numeric|min:3|max:100',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation Error',
                    'errors' => $validator->errors()
                ], 400);
            }

            $user = Auth::user();
            $user->wallet += $request->amount;
            $user->save();

            return response()->json([
                'message' => 'Money added to wallet successfully',
                'data' => [
                    'amount' => $request->amount,
                    'new_balance' => $user->wallet
                ]
            ], 200);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                'message' => 'Something went wrong'
            ], 500);
        }
    }
}
