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
     * Create a user for auth and generate a API token
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

        return response()->json(['token' => $user->createToken($user->email, $user->permissions)->plainTextToken],201);
    }

    /**
     * Login a user to generate a API token
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
            return response()->json(['token' => $user->createToken($user->email, $user->permissions)->plainTextToken],200);
        }

        return response()->json(['message'=> 'Unauthenticated.'], 401);

    }

    /**
     * Revoke the API token of the authenticated user
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message'=> 'Success.'], 200);
    }
}
