<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\SignupRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    /**
     * Create a user for auth
     *
     * @param SignupRequest $request
     * @return JsonResponse
     */
    public function signup(SignupRequest $request)
    {
        $user = new User();
        $user->name = $request->get('name');
        $user->email = $request->get('email');
        $user->password = bcrypt($request->get('password'));
        $user->permissions = $request->get('rules');
        $user->save();

        auth()->attempt(["email" => $request->get('email'), "password" => $request->get('password')]);

        return response()->json(['token' => $user->createToken($user->email, $user->permissions)->plainTextToken],200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request)
    {
        if(auth()->attempt(["email" => $request->get('email'), "password" => $request->get('password')]))
        {
            $user = auth()->user();
            $user->tokens()->where('name', $user->email)->delete();
            return response()->json(['token' => $user->createToken('token', $user->permissions)->plainTextToken],200);
        }

        return response()->json(['message'=> 'Unauthenticated.'], 401);

    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request)
    {
        // Revoke the token that was used to authenticate the current request...
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message'=> 'Success.'], 200);
    }
}
