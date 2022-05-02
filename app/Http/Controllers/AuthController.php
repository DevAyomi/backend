<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use App\Http\Resources\User as UserResource;

class AuthController extends Controller
{
    public function register(RegisterRequest $request){
       
        $validated = $request->validated();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
        ]);

       if(!$token = auth()->attempt($request->only('email', 'password'))){
         return abort(401);
       };

       return (new UserResource($request->user()))->additional([
            'meta' => [
                'token' => $token,
            ],
       ]);

    }

    public function login(LoginRequest $request){
        if(!$token = auth()->attempt($request->only('email', 'password'))){
         return response()->json([
            'errors' =>[
                'email' => ['Sorry we cant find you with those details'],
            ]
         ], 422);
        };

        return (new UserResource($request->user()))->additional([
            'meta' => [
                'token' => $token,
            ],
        ]);
    }

    public function user(Request $request){

        return new UserResource($request->user());

    }

    public function logout(){
        auth()->logout();

        return response()->json([
            'message' => 'User has been logged out successfully',
        ]);
    }
}
