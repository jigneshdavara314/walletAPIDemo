<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class CookieController extends Controller
{
    /**
     * Buy a cookie
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function buyCookies(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'quantity' => 'required|integer|min:1',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation Error',
                    'errors' => $validator->errors()
                ], 400);
            }

            $user = Auth::user();
            $total_cost = $request->quantity * 1;

            if ($user->wallet < $total_cost) {
                return response()->json([
                    'message' => 'Insufficient balance in wallet'
                ], 400);
            }

            $user->wallet -= $total_cost;
            $user->save();

            return response()->json([
                'message' => 'Cookie bought successfully',
                'data' => [
                    'quantity' => $request->quantity,
                    'total_cost' => $total_cost,
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
