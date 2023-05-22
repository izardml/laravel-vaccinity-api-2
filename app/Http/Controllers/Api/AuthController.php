<?php

namespace App\Http\Controllers\Api;

use App\Models\Society;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = [
            'id_card_number' => $request->id_card_number,
            'password' => $request->password,
        ];

        $society = Society::where($credentials)->with('regional')->first();

        if($society) {
            $token = md5($society->id_card_number);
            $society->update(['login_tokens' => $token]);
            $society->token = $token;

            return response()->json($society, 200);
        }

        return response()->json([
            'message' => 'ID Card Number or Password incorrect'
        ], 401);
    }

    public function logout(Request $request)
    {
        $society = Society::where('login_tokens', $request->token)->first();

        if($society) {
            $society->update(['login_tokens' => null]);

            return response()->json([
                'message' => 'Logout success'
            ], 200);
        }

        return response()->json([
            'message' => 'Invalid token'
        ], 401);
    }
}
