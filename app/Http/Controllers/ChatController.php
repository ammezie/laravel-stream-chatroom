<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GetStream\StreamChat\Client as StreamClient;

class ChatController extends Controller
{
    protected $client;
    protected $channel;

    public function __construct()
    {
        $this->client = new StreamClient(env("MIX_STREAM_API_KEY"), env("MIX_STREAM_API_SECRET"), null, null, 9);
        $this->channel = $this->client->Channel("messaging", "chatroom");
    }

    /**
     * Generate token for clientside use
     */
    public function generateToken(Request $request)
    {
        return response()->json([
            'token' => $this->client->createToken($request->user_id)
        ]);
    }

    public function leaveChannel(Request $request)
    {
        return $this->channel->removeMembers([$request->username]);
    }

    public function joinChannel(Request $request)
    {
        $username = explode('@', $request->email)[0];

        $this->channel->addMembers([$username]);

        return redirect('/home');
    }
}
