<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Contracts\Providers\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{

    const USER_ROLE = 2;

    public function register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:6',
            ]);
            if ($validator->fails()) {
                return response()->json($validator->messages(), 400);
            }
            $user = User::create([
                'name' => $request->get('name'),
                'email' => $request->get('email'),
                'password' => bcrypt($request->password),
                'role_id' => 2
            ]);
            $token = JWTAuth::fromUser($user);
            return response()->json(compact('user', 'token'), 201);
                } catch (\Throwable $th) {
            Log::error("Error in register: " . $th->getMessage());
                }
        
    }

    public function login(Request $request)
    {
        $input = $request->only('email', 'password');
        $jwt_token = null;
        $validation = $jwt_token = JWTAuth::attempt($input);

        if (!$validation) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid Email or Password',
            ], Response::HTTP_UNAUTHORIZED);
        }

        return response()->json([
            'success' => true,
            'token' => $jwt_token,
        ]);
    }


    public function logout()
    {

        auth()->logout();
        return response()->json(['message' => 'Successfully logged out']);


        // $this->validate($request, [
        //     'token' => 'required'
        // ]);
        // try {
        //     JWTAuth::invalidate($request->token);
        //     return response()->json([
        //         'success' => true,
        //         'message' => 'User logged out successfully'
        //     ]);
        // } catch (\Exception $exception) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Sorry, the user cannot be logged out'
        //     ],
        //         Response::HTTP_INTERNAL_SERVER_ERROR
        //     );
        // }
    }

    
}
