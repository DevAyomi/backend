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
         ]);
        };

        return (new UserResource($request->user()))->additional([
            'meta' => [
                'token' => $token,
            ],
        ]);
    }
}
