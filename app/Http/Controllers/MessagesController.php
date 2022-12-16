<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class MessagesController extends Controller
{
    public function sendMessage(Request $req)
    {
        try {
            $userId = auth()->user()->id;
            $party = $req->get('party_id');
            $user = User::find($userId);
            $userParty = $user->party()->wherePivot('user_id', $userId)->wherePivot('active', true)->find($party);
            $validator = Validator::make($req->all(), [
                'content' => 'required',
                'party_id' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json($validator->messages(), 400);
            }
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

    public function editMessage(Request $req)
    {
        try {
            $userId = auth()->user()->id;
            $user = User::find($userId);
            $party = $req->get('party_id');
            $messageId = $req->get('id');
            $isMine = Message::where('user_id', $userId)->find($messageId);
            $userParty = $user->party()->wherePivot('user_id', $userId)->wherePivot('active', true)->find($party);
            dd($userParty);
            $validator = Validator::make($req->all(), [
                'content' => 'required'
            ]);
            if ($validator->fails()) {
                return response()->json($validator->messages(), 400);
            }
            if ($isMine && $userParty) {
                $updatedMessage = Message::where('id', $messageId)->update([
                    'content' => $req->get('content')
                ]);
                return response()->json([
                    'success' => true,
                    'message' => 'Message edited',
                    'data' => $updatedMessage
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'You cannot edit that message'
                ], 500);
            }
        } catch (\Throwable $th) {
            Log::error("Error editing message: " . $th->getMessage());

            return response()->json([
                'success' => true,
                'message' => 'Could not edit message'
            ], 500);
        }
    }

    public function deleteMessage($id)
    {
        try {
            $userId = auth()->user()->id;
            $user = User::find($userId);
            $party = Message::where('user_id', $userId)->select('party_id')->first();
            $userParty = $user->party()->wherePivot('user_id', $userId)->wherePivot('active', true)->wherePivot('party_id', $party->party_id)->first();

            $isMine = Message::where('user_id', $userId)->find($id);
            if ($isMine && $userParty) {
                Message::where('id', $id)->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'Message deleted'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'You cannot delete that message'
                ], 500);
            }
        } catch (\Throwable $th) {
            Log::error("Error deleting message: " . $th->getMessage());

            return response()->json([
                'success' => true,
                'message' => 'Could not delete message'
            ], 500);
        }
    }

    public function getAllMessages($id){
        try {
            $userId = auth()->user()->id;
            $party = $id;
            $user = User::find($userId);
            $userParty = $user->party()->wherePivot('user_id', $userId)->find($party);
            if($userParty){
                $messages = Message::where('party_id', $id)->orderBy('id', 'DESC')->select('messages.content', 'messages.user_id', 'messages.created_at')->with('user:id,name')->get();
                return response()->json([
                    'success' => true,
                    'data' => $messages
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not in that party'
                ], 500);
            }
        } catch (\Throwable $th) {
            Log::error("Error getting messages: " . $th->getMessage());

            return response()->json([
                'success' => true,
                'message' => 'Could not get messages'
            ], 500);
        }
    }
}
