<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use App\Http\Requests\AuthLoginRequest;
use App\Http\Requests\AuthRegisterRequest;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login','register']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(AuthLoginRequest $request)
    {
        // Validate User
        $user = User::where('email', $request->input('email'))->first();

        // Get the token
        if ($user) {
            if (Hash::check($request->input('password'), $user->password)) {
                $token = auth()->login($user);
                return $this->respondWithToken($token);
            }
        }

        return response()->json(['error' =>  Hash::make($request->input('password'))], 401);
    }

    /**
     * Create a new user credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(AuthRegisterRequest $request)
    {
        $credentials = request(['name', 'email', 'phone', 'password']);

        // Check if email exsits
        if (User::where('email', $request->input('email'))->first()) {
            return response()->json(['error' => 'Email has been taken'], 409);
        }

        // Check if a name is available 
        $name = empty($request->input('name')) ? uniqid() : $request->input('name');

        if(!User::create([
            'name' => $name,
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
            'password' => Hash::make($request->input('password')),
        ])){
            return response()->json(['error' => 'Failed to save details'], 409);
        }

        return response()->json(['message' => 'Registration Successfull', 'redirect' => 'login'], 200);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }
}
