<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendMailable;

class HooksController extends Controller
{

    public function sendmails(Request $request)
    {
        $payload = $request->all();

        if ($payload['type'] == "message.new") {

            foreach ($payload["members"] as $member) {
                if ($member["user"]["online"] == false) {
                    $email = $member["user"]["id"] . "@gmail.com";
                    Mail::to($email)->send(new SendMailable($member["user"]["name"]));
                    return 'Email was sent';
                }
            }
        }
    }
}
