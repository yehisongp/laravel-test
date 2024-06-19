<?php

namespace App\Http\Controllers;

use App\Http\Requests\storeRegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserController extends Controller
{
    public function register(storeRegisterRequest $request)
    {
        try {
            DB::beginTransaction();

            $user = User::create($request->all());

            DB::commit();

            return response()->json([
                'message' => 'Usuario creado correctamente',
                'user' => $user
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Se presentó un inconveniente al registrar el usuario, por favor pongase en contacto con el equipo de soporte',
                $th->getLine(),
                $th->getMessage()
            ], 500);
        }
    }
    public function login(Request $request)
    {
        try {
            $credentials = $request->only('email', 'password');
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
            return $this->respondWithToken($token);

        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Se presentó un inconveniente al iniciar sesión, por favor pongase en contacto con el equipo de soporte',
                $th->getLine(),
                $th->getMessage()
            ], 500);
        }
    }
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
        ]);
    }
}
