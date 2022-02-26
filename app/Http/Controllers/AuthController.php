<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|min:8|string|unique:users,username',
            'email' => 'required|string|email|unique:users,email',
            'password' => 'required|string|min:8'
        ]);

        if ($validator->fails()) {
            return error_response($validator->errors, 401);
        }

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);

        $token = $user->createToken($request->username)->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'username' => $user->username,
                'email' => $user->email
            ]
        ], 201);
    }

    public function login(Request $request): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|min:8',
            'password' => 'required|string|min:8'
        ]);

        if ($validator->fails()) {
            return error_response($validator->errors(), 401);
        }

        $user = User::query()->where('username', $request->username)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return error_response('username or login is incorrect!', 401);
        }

        return response()->json([
            'token' => $user->createToken($user->username)->plainTextToken,
            'user' => [
                'username' => $user->username,
                'email' => $user->email
            ]
        ]);
    }

    public function whoAmI(): \Illuminate\Http\JsonResponse
    {
        return response()->json(['user' => [
            'id' => auth()->user()->id,
            'username' => auth()->user()->username,
            'email' => auth()->user()->email
        ]]);
    }
}
