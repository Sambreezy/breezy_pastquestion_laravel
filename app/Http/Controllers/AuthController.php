<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\PasswordReset;
use App\Exceptions\CustomException;
use App\Http\Controllers\Controller;
use App\Http\Requests\AuthLoginRequest;
use App\Http\Requests\AuthRegisterRequest;
use App\Http\Requests\AuthForgotPasswordRequest;
use App\Http\Requests\AuthResetPasswordRequest;
use App\Http\Requests\AuthChangePasswordRequest;
use App\Http\Requests\AuthVerifyEmailRequest;
use App\Http\Requests\AuthRefreshRequest;
use App\Http\Requests\AuthLogoutRequest;
use App\Http\Requests\AuthMeRequest;
use App\Traits\EmailSenderTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;


class AuthController extends Controller
{
    use EmailSenderTrait;

    /**
     * Permits login without password 
     *
     * @var boolean
     */
    protected $withoutPassword = false;

    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api')->except('login','register','forgotPassword','resetPassword','verifyEmail');
    }

    /**
     * Get a JWT via given credentials.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(AuthLoginRequest $request)
    {
        // Validate User
        $user = User::where('email', $request->input('email'))->first();

        // Get access token
        if ($user) {

            // Check if user has been banned
            if ($user->blocked == true) {
                return $this->forbiddenAccess('You are temporary banned, please contact support');
            }

            // Get an ovsettings value
            $email_verification = config('ovsettings.email_verification', false);
            $grace_period = config('ovsettings.grace_period', 10080);

            // Check if user has validated email
            if ($email_verification) {
                if (!$this->isEmailVerified($user, $grace_period)) {
                    return $this->forbiddenAccess('Your email is not yet verified, check your mailbox for the verification link');
                }
            }

            if (Hash::check($request->input('password'), $user->password) || $this->withoutPassword) {


                // Get JWT token
                $token = auth()->login($user);

                // Return success
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

        // Return Failure
        return $this->authenticationFailure('Incorrect Login details');
    }

    /**
     * Create a new user credentials.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(AuthRegisterRequest $request)
    {
        // Check if email exists
        if (User::withTrashed()->where('email', $request->input('email'))->first()) {
            return $this->badRequest('Email has been taken');
        }

        // Check if a name is available 
        $name = empty($request->input('name')) ? uniqid() : $request->input('name');

        // Fill the user model
        $user = new User;
        $user->fill($request->toArray());

        // Additional params
        $user->name = $name;
        $user->email = $request->input('email');
        $user->password = Hash::make($request->input('password'));

        // Continue on success
        if (!$user->save()) {
            return $this->requestConflict('Failed to save details');
        }

        // Get an ovsettings value
        $email_verification = config('ovsettings.email_verification', false);

        // Check if email verification is active
        if ($email_verification) {

            // Send an email to user containing email validation link
            $this->sendEmail(
                $user->email,
                config('constants.mail.verification'),
                env('APP_URL').'/api/auth/verify?email='.$user->email.'&token='.$this->createEmailVerificationToken($user->email),
            );
        }

        // Return success
        return $this->entityCreated($user,'Registration successful');
    }

    /**
     * Creates a reset user password token.
     *
     * @param  \Illuminate\Http\Request  $request
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
        $password_reset = new PasswordReset;
        $id = Str::random(10);
        $token = Str::random(10);
        $password_reset->id = $id;
        $password_reset->token = Hash::make($token);
        $password_reset->email = $request->input('email');

        // Save new token
        if (!$password_reset->save()){
            return $this->unavailableService('Failed to reset password');
        }

        /**
         * Note that in order to make the search more precise and faster
         * The id of the password reset row has been appended to the token
         * that is being sent to the user.
         */
        // Send an email to user containing reset link
        $this->sendEmail(
            $password_reset->email,
            config('constants.mail.reset'),
            $request->input('reset_form_link').'?token='.$token.$id
        );

        // Return success
        return $this->actionSuccess('Reset successful, please check email for link to reset password');
    }

    /**
     * Reset a user password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resetPassword(AuthResetPasswordRequest $request)
    {
        /**
         * Id of the password reset order is extracted from the user input using substr
         */
        // Check if email and id combination exists in password reset table
        $password_reset = PasswordReset::where('email', $request->input('email'))
        ->where('id', substr($request->input('token'),-10))
        ->first();

        if (!$password_reset) {
            return $this->notFound('Ensure that the email belongs to you');
        }

        // Check if token matches
        if (!Hash::check(substr($request->input('token'),0,-10), $password_reset->token)) {
            return $this->authenticationFailure('Unable to reset password');
        }

        // Get an ovsettings value
        $token_exp_time = config('ovsettings.reset_password_token_exp_time', 60);

        // Check if token has expired
        if ((time() + ($token_exp_time*60)) <  strtotime($password_reset->created_at)) {
            return $this->forbiddenAccess('Token has expired');
        }

        // Make a new password
        $user = User::where('email', $request->input('email'))->first();
        if (!$user) {
            return $this->notFound('Ensure that the email belongs to you');
        }
        $user->password = Hash::make($request->input('new_password'));

        // Save new password
        if (!$user->save()){
            return $this->unavailableService('Failed to reset password');
        }

        // Send an email to user informing about password change
        $this->sendEmail(
            $user->email,
            config('constants.mail.info'),
            'Your password was changed on '.date('Y-m-d H:i:s',time())
        );

        // Return success
        return $this->actionSuccess('Reset successful, please login');
    }

    /**
     * Change a user password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function changePassword(AuthChangePasswordRequest $request)
    {
        // Check if user exists
        $user = User::find(auth()->user()->id);

        // Exit if user was not found
        if (!$user) {
            return $this->notFound('Unable to identify account');
        }

        // Check if old password input matches password record
        if (!Hash::check($request->input('old_password'), $user->password)) {
            return $this->requestConflict('Old password is incorrect');
        }

        // Make a new password
        $user->password = Hash::make($request->input('new_password'));

        // Save new password
        if (!$user->save()){
            return $this->unavailableService('Failed to save password');
        }

        // Send an email to user informing about password change
        $this->sendEmail(
            $user->email,
            config('constants.mail.info'),
            'Your password was changed on '.date('Y-m-d H:i:s',time())
        );

        // Logout user
        auth()->logout();

        // Return success
        return $this->actionSuccess('Reset successful, please login again');
    }

    /**
     * Get the authenticated user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function me(AuthMeRequest $request)
    {
        // Return the request sender details 
        return $this->success(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(AuthLogoutRequest $request)
    {
        // Invalidate request sender token
        auth()->logout();

        // Return success
        return $this->actionSuccess('Successfully logged out');
    }

    /**
     * Refresh a token.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh(AuthRefreshRequest $request)
    {
        // Return a new token to request sender and invalidate original token
        return $this->success($this->respondWithToken(auth()->refresh()));
    }

    /**
     * Get the token array structure.
     *
     * @param string $token
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
     * @param EloquentModel $user
     * @param integer $grace_period
     * @return boolean
     */
    public function isEmailVerified($user, $grace_period=0)
    {
        try {
            // Check if user is already verified
            if ($user->email_verified_at) {
                
                // Return success
                return true;
            }

            // Check if grace period is still active
            if ($grace_period != 0) {

                /**
                 * Gets time in which user account was created 
                 * Adds stated grace period to time user account was created
                 * Checks if user is still within grace period
                 * Lets user access the app.
                 */
                $grace_period = strtotime($user->created_at) + ($grace_period*60);
                if ($grace_period > time()) {

                    // Return success
                    return true;
                }
            }

            // Send an email to user containing email validation link
            $this->sendEmail(
                $user->email,
                config('constants.mail.verification'),
                env('APP_URL').'/api/auth/verify?email='.$user->email.'&token='.$this->createEmailVerificationToken($user->email),
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
     * @return boolean false
     * @return string
     */
    public function createEmailVerificationToken($email)
    {
        // Get an ovsettings value
        $token_key = config('ovsettings.email_verification_token', false);

        // Check if key exists and is more than 32 characters
        if (!$token_key || strlen($token_key)<32) {
            throw new CustomException("Error, OV_EMAIL_TOKEN is not set. Run artisan ovkey:generate command");
        }

        // Create token generator
        $token_maker = function ($token_key, $email){

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

                // Return results
                return $token;

            } catch (\Throwable $th) {
                return false;
            }
        };

        // Run token generator
        $token = $token_maker($token_key, $email);

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
     * @param  \Illuminate\Http\Request  $request
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
            return $this->requestConflict('Currently unable to validate email');
        }

        // Additional params
        $user->email_verified_at = date('Y-m-d H:i:s', time());

        // Return success
        if ($user->save()) {
            // return $this->actionSuccess('Successfully validated email');
            return view('notifications.email_verification')->with('status','success');
        } else {
            // return $this->requestConflict('Currently unable to properly validate email');
            return view('notifications.email_verification')->with('status','failure');
        }
    }

    /**
     * Enable login without password
     * 
     * @param void
     * @return object
     */
    public function withoutPassword()
    {
        $this->withoutPassword = true;
        return $this;
    }
}
