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
                'message' => 'Game created',
                'data' => $game
            ]);
        } catch (\Throwable $th) {
            Log::error("Error creating game: " . $th->getMessage());

            return response()->json([
                'success' => true,
                'message' => 'Game could not be created'
            ], 500);        }
        
    }

    public function getAllGames() {
        try {
            $games = Game::query()->get();
            return response()->json([
                'success' => true,
                'message' => 'Games retrieved',
                'data' => $games
            ]);
        } catch (\Throwable $th) {
            Log::error("Error retrieving games: " . $th->getMessage());

            return response()->json([
                'success' => true,
                'message' => 'Could not retrieve games'
            ], 500);
        }
    }
    public function getGameById($id)
    {
        try {
            $game = Game::where('id', $id)->get();
            return response()->json([
                'success' => true,
                'message' => 'Game retrieved',
                'data' => $game
            ]);
        } catch (\Throwable $th) {
            Log::error("Error retrieving game: " . $th->getMessage());

            return response()->json([
                'success' => true,
                'message' => 'Could not retrieve game'
            ], 500);
        }
    }

    public function getGameByName($name)
    {
        try {
            $game = Game::where('name', 'like', '%'.$name.'%')->get();
            return response()->json([
                'success' => true,
                'message' => 'Game retrieved',
                'data' => $game
            ]);
        } catch (\Throwable $th) {
            Log::error("Error retrieving game: " . $th->getMessage());

            return response()->json([
                'success' => true,
                'message' => 'Could not retrieve game'
            ], 500);
        }
    }

    public function deleteGameById($id){
        try {
            $game = Game::where('id', $id)->delete();
            return response()->json([
                'success' => true,
                'message' => 'Game deleted',
                'data' => $game
            ]);
        } catch (\Throwable $th) {
            Log::error("Error retrieving game: " . $th->getMessage());

            return response()->json([
                'success' => true,
                'message' => 'Could not retrieve game'
            ], 500);
        }
    }

}
