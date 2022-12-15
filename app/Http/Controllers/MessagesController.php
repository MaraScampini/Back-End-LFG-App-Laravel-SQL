<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MessagesController extends Controller
{
    public function sendMessage(Request $req)
    {
        try {
            $userId = auth()->user()->id;
            $party = $req->get('party_id');
            $user = User::find($userId);
            $userParty = $user->party()->wherePivot('user_id', $userId)->find($party);
            if ($userParty) {

                $message = Message::create([
                    'content' => $req->get('content'),
                    'user_id' => $userId,
                    'party_id' => $req->get('party_id')
                ]);
                return response()->json([
                    'success' => true,
                    'message' => 'Message sent',
                    'data' => $message
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not in that party'
                ], 500);
            }
        } catch (\Throwable $th) {
            Log::error("Error sending message: " . $th->getMessage());

            return response()->json([
                'success' => true,
                'message' => 'Could not send message'
            ], 500);
        }
    }

    
}
