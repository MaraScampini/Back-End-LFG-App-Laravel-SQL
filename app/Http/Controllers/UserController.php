<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function profile()
    {
        try {
            $user = auth()->user();

            return response()->json([
                'success' => true,
                'message' => 'Profile successfully retrieved',
                'data' => $user
            ]);
        } catch (\Throwable $th) {
            Log::error("Error retrieving user: " . $th->getMessage());

            return response()->json([
                'success' => true,
                'message' => 'User could not be retrieved'
            ], 500);
        }
    }
}