<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    public function register(Request $request){

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if($validator->fails()){
            return response()->json(['status' => false,'message' => $validator->errors()], 200);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user'
        ]);

        $token = JWTAuth::fromUser($user);

        $res = array(
            'status' => true,
            'message' => 'User registered successfully',
            'user' => $user,
            'token' => $token 
        );
        return response()->json($res, 200);
    }

    public function login(Request $request){

        $credentials = $request->only('email', 'password');

        try {
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json(['status' => false,'message' => 'Invalid Credentials'], 200);
            }
        } catch (JWTException $e) {
            return response()->json(['status'=> true ,'message' => 'Could not create token'], 200);
        }

        $user = auth()->user();

        return response()->json([
            'status' => true,
            'message' => 'Login successfully',
            'user' => $user,
            'token' => $token
        ]);
    }
}
