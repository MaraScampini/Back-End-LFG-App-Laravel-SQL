<?php

namespace App\Http\Controllers;

use App\Models\Data;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function profile()
    {
        try {
            $userId = auth()->user()->id;

            $fullUser = Data::select('data.*')->with('user:id,email')->find($userId);
            
            return response()->json([
                'success' => true,
                'message' => 'Profile successfully retrieved',
                'data' => $fullUser
            ]);
        } catch (\Throwable $th) {
            Log::error("Error retrieving user: " . $th->getMessage());

            return response()->json([
                'success' => true,
                'message' => 'User could not be retrieved'
            ], 500);
        }
    }

    public function getUserById($id)
    {
        try {

            $fullUser = Data::select('data.*')->with('user:id,email')->find($id);

            return response()->json([
                'success' => true,
                'message' => 'Profile successfully retrieved',
                'data' => $fullUser
            ]);
        } catch (\Throwable $th) {
            Log::error("Error retrieving user: " . $th->getMessage());

            return response()->json([
                'success' => true,
                'message' => 'User could not be retrieved'
            ], 500);
        }
    }

    public function getAllUsers() {
        try {
            $users = User::get();

            return response()->json([
                'success' => true,
                'message' => 'Users successfully retrieved',
                'data' => $users
            ]);
        } catch (\Throwable $th) {
            Log::error("Error retrieving users: " . $th->getMessage());

            return response()->json([
                'success' => true,
                'message' => 'Users could not be retrieved'
            ], 500);
        }


    }

}
