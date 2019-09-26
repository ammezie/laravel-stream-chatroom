<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GetStream\StreamChat\Client as StreamClient;

class ChatController extends Controller
{
    /**
     * Generate token for clientside use
     */
    public function generateToken(Request $request)
    {
        $client = new StreamClient(env("MIX_STREAM_API_KEY"), env("MIX_STREAM_API_SECRET"), null, null, 9);

        return response()->json([
            'token' => $client->createToken($request->user_id)
        ]);
    }
}
