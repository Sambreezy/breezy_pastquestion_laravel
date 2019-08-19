<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use App\Http\Requests\AuthLoginRequest;
use App\Http\Requests\AuthRegisterRequest;
use App\Http\Requests\AuthResetPasswordAlphaRequest;
use App\Http\Requests\AuthResetPasswordOmegaRequest;
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
     * Creates a reset user password token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function resetPasswordAlpha(AuthResetPasswordAlphaRequest $request)
    {
        // Check if email exsits
        $user = User::where('email', $request->input('email'))->first();
        if (!$user) {
            return $this->notFound('Ensure that the email belongs to you');
        }

        // Make a new token
        $new_reset_token = uniqid();
        $user->remember_token = Hash::make($new_reset_token);

        // Save new token
        if (!$user->save()){
            return $this->actionFailure('Failed to reset password');
        }

        // Send an email to user
        Helper::sendSimpleMail('key',['email'=>$user->email,'message'=>$new_reset_token, 'html_location'=>'']);

        // Retrun success
        return $this->actionSuccess('Reset successfull, please check email for link to reset password');
    }

    /**
     * Reset a user password.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function resetPasswordOmega(AuthResetPasswordOmegaRequest $request)
    {
        // Check if email exsits
        $user = User::where('email', $request->input('email'))->first();
        if (!$user) {
            return $this->notFound('Ensure that the email belongs to you');
        }

        if (!Hash::check($request->input('key'), $user->remember_token)) {
            return $this->unauthorized('Unable to reset password');
        }

        // Make a new password
        $user->password = Hash::make($request->input('new_password'));

        // Save new password
        if (!$user->save()){
            return $this->actionFailure('Failed to reset password');
        }

        // Send an email to user
        Helper::sendSimpleMail('key',['email'=>$user->email,'message'=>'your password was successfully reset', 'html_location'=>'']);

        // Retrun success
        return $this->actionSuccess('Reset successfull, please login');
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

        if ($user->id !== auth()->user()->id) {
            return $this->unauthorized('This account does not belong to you');
        }

        if (!Hash::check($request->input('old_password'), $user->password)) {
            return $this->actionFailure('Old password is incorrect');
        }

        // Make a new password
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
