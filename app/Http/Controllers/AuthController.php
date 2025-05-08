<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AuthController extends Controller
{   

    /**
     * @OA\Post(
     *     tags={"authentication"},
     *     summary="Login with user",
     *     path="/api/login",
     *     @OA\RequestBody(
     *          @OA\MediaType(
     *             mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  required={"email", "password"},
     *                  @OA\Property(property="email", type="string", example="admin@backend.com.br"),
     *                  @OA\Property(property="password", type="string", format="password")
     *          )
     *     ),
     * ),
     *     @OA\Response(response="200", description=""),
     * ),
     */
    public function login(Request $request)
    {
        $credentials = $request->only(['email', 'password']);

        if (!$token = auth('api')->attempt($credentials)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $user = User::where('email', $request->email)->first();
        
        return response()->json([
            'name' => $user->name,
            'jwt_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 30
        ]);
    }


    /**
     * @OA\Post(
     *     tags={"authentication"},
     *     summary="Logout",
     *     path="/api/logout",
     *     security={ {"bearerToken": {}} },
     *     @OA\Response(response="200", description=""),
     * ),
     */
    public function logout()
    {
        auth('api')->logout();
        return response()->json(['message' => 'User successfully logged out']);
    }
}