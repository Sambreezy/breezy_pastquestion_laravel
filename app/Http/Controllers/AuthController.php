<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use App\Http\Requests\AuthLoginRequest;
use App\Http\Requests\AuthRegisterRequest;
use App\Http\Requests\AuthResetPasswordRequest;
use App\Http\Requests\AuthChangePasswordRequest;
use App\Models\User;
use App\Helpers\Helper;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login','register','reset']]);
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

                return $this->success([
                    'id'    => $user->id,
                    'name'  => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'votes' => $user->votes,
                    'picture' => $user->picture,
                    'description' => $user->description,
                    'jwt'   => $this->respondWithToken($token),
                ]);
            }
        }

        return $this->unauthorized('Incorrect Login details');
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
            return $this->actionFailure('Email has been taken');
        }

        // Check if a name is available 
        $name = empty($request->input('name')) ? uniqid() : $request->input('name');

        if(!User::create([
            'name' => $name,
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
            'password' => Hash::make($request->input('password')),
        ])){
            return $this->actionFailure('Failed to save details');
        }

        return $this->actionSuccess('Registration Successfull');
    }

    /**
     * Reset a user password.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function resetPassword(AuthResetPasswordRequest $request)
    {
        // Check if email exsits
        $user = User::where('email', $request->input('email'))->first();
        if (!$user) {
            return $this->notFound('Ensure that the email belongs to you');
        }

        // Make new password
        $new_password = uniqid();
        $user->password = Hash::make($new_password);

        // Save new password
        if (!$user->save()){
            return $this->actionFailure('Failed to reset password');
        }

        // send an email to user
        Helper::sendSimpleMail('key',['email'=>$user->email,'message'=>"Your new password is $new_password", 'html_location'=>'']);

        return $this->actionSuccess('Reset successfull, please check email for new password');
    }

    /**
     * Change a user password.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function changePassword(AuthChangePasswordRequest $request)
    {
        // Check if user exsits
        $user = User::find($request->input('id'));

        if (!$user) {
            return $this->notFound('Unable to identify account');
        }

        if (!Hash::check($request->input('old_password'), $user->password)) {
            return $this->actionFailure('Old password is incorrect');
        }

        // Make new password
        $user->password = Hash::make($request->input('new_password'));

        // Save new password
        if (!$user->save()){
            return $this->actionFailure('Failed to save password');
        }

        return $this->actionSuccess('Reset Successfull');
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return $this->success(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return $this->actionSuccess('Successfully logged out');
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->success($this->respondWithToken(auth()->refresh()));
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
