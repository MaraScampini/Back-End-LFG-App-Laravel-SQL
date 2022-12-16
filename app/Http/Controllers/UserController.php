<?php

namespace App\Http\Controllers;

use App\Models\Data;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

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

    public function getAllUsers()
    {
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

    public function updateProfile(Request $req)
    {

        try {
            $userId = auth()->user()->id;
            $validator = Validator::make($req->all(), [
                'surname' => 'string|max:100',
                'address' => 'string',
                'age' => 'integer',
                'steam_username' => 'string',
                'user_id' => 'required'
            ]);
            if ($validator->fails()) {
                return response()->json($validator->messages(), 400);
            }
            $input = array_filter($req->all());
            $fullUser = Data::where('user_id', $userId)->update($input);

            return response()->json([
                'success' => true,
                'message' => 'Users successfully retrieved',
                'data' => $fullUser
            ]);
        } catch (\Throwable $th) {
            Log::error("Error updating user: " . $th->getMessage());

            return response()->json([
                'success' => true,
                'message' => 'User data could not be updated'
            ], 500);
        }
    }

    public function deleteMyProfile()
    {
        try {
            $userId = auth()->user()->id;
            if (auth()->user()->role_id != 1) {
                User::where('id', $userId)->delete();

                return response()->json([
                    'success' => true,
                    'message' => 'User successfully deleted',
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Admin profiles cannot be deleted'
                ], 400);
            }
        } catch (\Throwable $th) {
            Log::error("Error deleting user: " . $th->getMessage());

            return response()->json([
                'success' => true,
                'message' => 'User could not be deleted'
            ], 500);
        }
    }

    public function deleteProfile($id)
    {
        try {
            $user = User::where('id', $id)->first();

            if ($user->role_id != 1) {

                User::where('id', $id)->delete();

                return response()->json([
                    'success' => true,
                    'message' => 'User successfully deleted',
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Admin profiles cannot be deleted'
                ], 400);
            }
        } catch (\Throwable $th) {
            Log::error("Error deleting user: " . $th->getMessage());

            return response()->json([
                'success' => true,
                'message' => 'User could not be deleted'
            ], 500);
        }
    }
}
