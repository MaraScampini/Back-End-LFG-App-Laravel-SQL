<?php

namespace App\Http\Controllers;

use App\Models\Party;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PartyController extends Controller
{
    public function createParty(Request $req)
    {
        try {
            $userId = auth()->user()->id;
            $validator = Validator::make($req->all(), [
                'name' => 'required|string|max:100',
                'game_id' => 'required|integer'
            ]);
            if ($validator->fails()) {
                return response()->json($validator->messages(), 400);
            }

            $party = Party::create([
                'name' => $req->get('name'),
                'game_id' => $req->get('game_id')
            ]);

            $party->user()->attach($userId, ['owner' => true, 'active' => true]);

            return response()->json([
                'success' => true,
                'message' => 'Party created',
            ]);
        } catch (\Throwable $th) {
            Log::error("Error creating party: " . $th->getMessage());

            return response()->json([
                'success' => true,
                'message' => 'Party could not be created'
            ], 500);
        }
    }

    public function joinPartyById($id)
    {
        try {
            $userId = auth()->user()->id;
            $party = Party::find($id);
            if (!$party) {
                return response()->json([
                    'success' => false,
                    'message' => 'Party does not exist in database'
                ], 500);
            }
            $active = $party->user()->wherePivot('active', true)->find($userId);
            $existing = $party->user()->find($userId);
             if ($active && $party) {
                return response()->json([
                    'success' => false,
                    'message' => 'You already are in that party',
                ]);
            } else if ($existing && $party) {
                $party->user()->updateExistingPivot($userId, ['owner' => false, 'active' => true]);
                return response()->json([
                    'success' => true,
                    'message' => 'Party re-joined',
                ]);
            } else {
                $party->user()->attach($userId, ['owner' => false, 'active' => true]);
                return response()->json([
                    'success' => true,
                    'message' => 'Party joined',
                ]);
            }
        } catch (\Throwable $th) {
            Log::error("Error joining party: " . $th->getMessage());

            return response()->json([
                'success' => true,
                'message' => 'Could not join party'
            ], 500);
        }
    }

    public function leaveParty($id)
    {
        try {
            $userId = auth()->user()->id;
            $party = Party::find($id);
            $owner = $party->user()->wherePivot('owner', true)->find($userId);
            $active = $party->user()->wherePivot('active', true)->find($userId);
            if(!$active) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not in that party',
                ]);
            } else if ($owner) {
                return response()->json([
                    'success' => false,
                    'message' => 'The owner cannot leave the party, delete it instead'
                ]);
            } else {
                $party->user()->updateExistingPivot($userId, ['owner' => false, 'active' => false]);
                return response()->json([
                    'success' => true,
                    'message' => 'You have left the party',
                ]);
            }
        } catch (\Throwable $th) {
            Log::error("Error joining party: " . $th->getMessage());

            return response()->json([
                'success' => true,
                'message' => 'Could not leave party'
            ], 500);
        }
    }

    public function deleteParty($id)
    {
        try {
            $userId = auth()->user()->id;
            $party = Party::find($id);
            if(!$party) {
                return response()->json([
                    'success' => false,
                    'message' => 'The party does not exist',
                ]);
            }
            $owner = $party->user()->wherePivot('owner', true)->find($userId);
            if ($owner) {
                $party->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'Party deleted'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Only the owner can delete the party',
                ]);
            }
        } catch (\Throwable $th) {
            Log::error("Error deleting party: " . $th->getMessage());

            return response()->json([
                'success' => true,
                'message' => 'Could not delete party'
            ], 500);
        }
    }

    public function getPartyByGame($id)
    {
        try {
            $parties = Party::where('game_id', $id)->get();
            return response()->json([
                'success' => true,
                'message' => 'Parties found',
                'data' => $parties
            ]);
        } catch (\Throwable $th) {
            Log::error("Error getting parties: " . $th->getMessage());

            return response()->json([
                'success' => true,
                'message' => 'Could not get parties'
            ], 500);
        }
    }

    public function getMyParties()
    {
        try {
            $userId = auth()->user()->id;
            $user = User::find($userId);
            $myParties = $user->party()->wherePivot('user_id', $userId)->get();
            return response()->json([
                'success' => true,
                'message' => 'Parties found',
                'data' => $myParties
            ]);
        } catch (\Throwable $th) {
            Log::error("Error getting parties: " . $th->getMessage());

            return response()->json([
                'success' => true,
                'message' => 'Could not get parties'
            ], 500);
        }
    }
}
