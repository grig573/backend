<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $data = $request->validated();
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password'])

        ]);

        $token = $user->createToken('auth_token')->plainTextToken;
        $cookie = cookie('token',$token,60 * 24);

        return response()->json([
            'user' => new UserResource($user),
        ])->withCookie($cookie);
       
    }


    public function login(LoginRequest $request)
    {
        $data = $request->validated();
        $user = User::where('email',$data['email'])->first();
        if(!$user || !Hash::check($data['password'],$user->password)){
           return response()->json([
            'message' => 'Email or password is wrong'
           ],401);
        }

        $token = $user->createToken('auth_token')->plainTaxtToken;
        $cookie = cookie('toke', $token,60 *24);
        return response()->json([
            'user' => new UserResource($user),
        ])->withCookie($cookie);
    }
}
