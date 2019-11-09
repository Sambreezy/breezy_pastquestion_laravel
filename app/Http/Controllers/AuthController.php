<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use App\Http\Requests\AuthLoginRequest;
use App\Http\Requests\AuthRegisterRequest;
use App\Http\Requests\AuthForgotPasswordRequest;
use App\Http\Requests\AuthResetPasswordRequest;
use App\Http\Requests\AuthChangePasswordRequest;
use App\Http\Requests\AuthVerifyEmailRequest;
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
        $this->middleware('auth:api', ['except' => [
            'login','register','forgotPassword','resetPassword','verifyEmail'
        ]]);
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

            // Check if user has been banned
            if ($user->blocked == true) {
                return $this->forbidden('You are temporary banned, please contact support');
            }

            // Get an ovsettings value
            $email_verification = config('ovsettings.email_verification', false);
            $grace_period = config('ovsettings.grace_period', 10080);

            // Check if user has validated email
            if ($email_verification) {
                if (!$this->isEmailVerified($user, $grace_period)) {
                    return $this->forbidden('Your email is not yet verified, check mail for verification link');
                }
            }

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

        // Check if email exists
        if (User::withTrashed()->where('email', $request->input('email'))->first()) {
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

        // Get an ovsettings value
        $email_verification = config('ovsettings.email_verification', false);

        // Check if email verification is active
        if ($email_verification) {
            
            // Send an email to user containing email validation link
            $this->sendEmail(
                $request->input('email'),
                'email-verification',
                $this->createEmailVerificationToken($request->input('email'))
            );
        }

        return $this->actionSuccess('Registration Successful');
    }

    /**
     * Creates a reset user password token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function forgotPassword(AuthForgotPasswordRequest $request)
    {
        // Check if email exists
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
        $this->sendEmail($user->email,'forgot-password', $new_reset_token);

        // Return success
        return $this->actionSuccess('Reset successful, please check email for link to reset password');
    }

    /**
     * Reset a user password.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function resetPassword(AuthResetPasswordRequest $request)
    {
        // Check if email exists
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
        $this->sendEmail($user->email,'reset-password','your password was successfully reset');

        // Return success
        return $this->actionSuccess('Reset successful, please login');
    }

    /**
     * Change a user password.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function changePassword(AuthChangePasswordRequest $request)
    {
        // Check if user exists
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

        // Send an email to user informing about password change
        $this->sendEmail(
            $user->email,
            'password-reset-info',
            'your password was changed on '.date('Y-m-d H:i:s',time())
        );

        // Logout user
        auth()->logout();
        
        // Return success
        return $this->actionSuccess('Reset Successful, Please Login');
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

    /**
     * Validates an email verification token.
     *
     * @param object $user
     * @param integer $grace_period
     * 
     * @return boolean
     */
    public function isEmailVerified($user=null, $grace_period=0)
    {
        try {
            // Check if user is already verified
            if ($user->email_verified_at) {

                // Return success
                return true;
            }

            if ($grace_period != 0) {

                /**
                 * Gets time in which user account was created 
                 * Adds stated grace period to time user account was created
                 * Checks if user is still within grace period
                 * Lets user access the app and still create a verification email
                 */
                $grace_period = strtotime($user->created_at) + ($grace_period*60);
                if ($grace_period > time()) {

                    // Send an email to user containing email validation link
                    $this->sendEmail(
                        $user->email,
                        'email-verification',
                        $this->createEmailVerificationToken($user->email)
                    );

                    // Return success
                    return true;
                }
            }

            // Send an email to user containing email validation link
            $this->sendEmail(
                $user->email,
                'email-verification',
                $this->createEmailVerificationToken($user->email)
            );

            // Return failure
            return false;

        } catch (\Throwable $th) {

            // Return failure
            return false;
        }
    }

    /**
     * Creates an email verification token.
     *
     * @param string $email
     * 
     * @return boolean false
     * @return string
     */
    public function createEmailVerificationToken($email)
    {
        // Get an ovsettings value
        $token_key = config('ovsettings.email_verification_token_key', false);

        function tokenMaker($token_key, $email){

            // Check if key exists and is more than 32 characters
            if (!$token_key || strlen($token_key)<32) {
                yield false;
            }

            // Make a new token
            try {
                // Split both strings to arrays of grouped types and merge
                $array = array_merge(
                    str_split(strtok($email,'@'), 1), 
                    str_split($token_key, 1)
                );

                // Sort array values in order of natural numbers
                natsort($array);

                // Take all the array keys only
                $array = array_keys($array);
                $token = '';

                // Mesh array key and values in to a string
                foreach ($array as $key => $value) {
                    $token .= $key.$value; 
                }

                // Hash the string
                $token = md5($token);

                // Yield results
                yield $token;

            } catch (\Throwable $th) {
                yield false;
            }
        }

        // Run tokenMaker generator
        foreach (tokenMaker($token_key, $email) as $key) {
            $token = $key;
        }

        // Return success
        if (strlen($token) == 32 && ctype_xdigit($token)) {
            return $token;
        }

        // Return Failure
        return false;
    }

    /**
     * Verify a user email.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyEmail(AuthVerifyEmailRequest $request)
    {
        // Check email
        $user = User::where('email', $request->input('email'))->first();
        if (!$user) {
            return $this->notFound('Ensure that the email belongs to you');
        }

        // Recreate token
        $token = $this->createEmailVerificationToken($request->input('email'));

        // Compare token
        if ($token !== $request->input('token')) {
            return $this->actionFailure('Currently unable to validate email');
        }

        // Time stamp validation
        $user->email_verified_at = date('Y-m-d H:i:s', time());
        if (!$user->save()) {
            return $this->failure('Currently unable to properly validate email');
        }

        // Return success
        return $this->actionSuccess('Successfully validated email');
    }

    /**
     * Prepares an email with verification token to be sent.
     *
     * @param string $email
     * @param string $topic
     * @param string $message
     *
     * @return boolean
     */
    public function sendEmail($email, $topic, $message=null)
    {
        // Email parameters
        if (!$email || !$topic) {
            return false;
        }

        // Send an email to user containing the appropriate email subject
        // Place email sender function here
        Helper::sendSimpleMail($email.' '.$topic.' '.$message);

        // Return success
        return true;
    }
}
