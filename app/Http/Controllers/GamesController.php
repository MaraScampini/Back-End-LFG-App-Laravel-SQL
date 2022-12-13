<?php

namespace App\Http\Controllers;

use App\Models\Game;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GamesController extends Controller
{
    public function addGame(Request $request)
    {
        try {

            $userId = auth()->user()->id;

            $game = Game::create([
                'name' => $request->get('name'),
                'genre' => $request->get('genre'),
                'FTP' => $request->get('FTP'),
                'user_id' => $userId
            ]);


            return response()->json([
                'success' => true,
                'message' => 'Game created' . $game
            ]);
        } catch (\Throwable $th) {
            Log::error("Error adding super admin role: " . $th->getMessage());

            return response()->json([
                'success' => true,
                'message' => 'SuperAdmin could not be added to user'
            ], 500);        }
        
    }

}
