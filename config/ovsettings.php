<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Reset Password Token Expiration Time
    |--------------------------------------------------------------------------
    |
    | This value is the reset password token expiration time of your application,
    | This value is used by the jwt package to set the application's reset
    | password token expiration time, the value is stated in minutes by default
    | is set at 30 which is equal to half an hour this value can not be less than
    | 1 minute and should be kept below 60 minutes (1 hour) for better security.
    */
    'reset_password_token_exp_time' => 30,

    /*
    |--------------------------------------------------------------------------
    | Email Verification
    |--------------------------------------------------------------------------
    |
    | This value is used by the email verification functions and can only be  
    | set to boolean (true) or (false) setting this to true will activate email 
    | validation checks, this can be changed at any point in the life of the 
    | of the application but it is recommended to set it this before deploying
    */
    'email_verification' => env('OV_EMAIL_VERIFICATION',true),

    /*
    |--------------------------------------------------------------------------
    | Grace Period For Email Verification
    |--------------------------------------------------------------------------
    |
    | This value is used by the isEmailVerified function and should be set to an
    | unsigned integer, this value can be set to (int) 0 to ensure instant  
    | verification before access to app, please note that value is specified in 
    | minutes for this value to be effective email_verification must be set to true
    |
    */
    'grace_period' => 10080,

    /*
    |--------------------------------------------------------------------------
    | Email Verification Key
    |--------------------------------------------------------------------------
    |
    | This key is used by the email verification token function and should 
    | be set to a random, 32 character string, otherwise these generated token
    | can be guessed. Please do this before deploying an application!
    | for this value to be effective email_verification must be set to true
    |
    */
    'email_verification_token' => env('OV_EMAIL_TOKEN'),

    /*
    |--------------------------------------------------------------------------
    | Api Exception Handler
    |--------------------------------------------------------------------------
    |
    | This is used by the exception handler located at App\Exceptions\Handler.php
    | to set exception handling to use json responses from the ApiResponderTrait
    | located at App\Traits\ApiResponderTrait.php as return values for all laravel
    | and custom exceptions. Set to false to return laravel exception render.
    |
    */
    'api_exception_handler' => env('OV_EXCEPTION_HANDLER',true),

    /*
    |--------------------------------------------------------------------------
    | Encryption And Decryption Key
    |--------------------------------------------------------------------------
    |
    | This key is used by the various crypt functions for encryption or
    | decryption of sensitive assets and should be set to a random,
    | 32 character string, otherwise these generated token
    | can be guessed. Please do this before deploying an application!
    |
    */
    'cipher_token' => env('OV_CIPHER_TOKEN'),

    /*
    |--------------------------------------------------------------------------
    | Mock Mail
    |--------------------------------------------------------------------------
    |
    | This value is used by the email sending functions to determine when to
    | send an actual email to the giving address or send to a predefined mock 
    | address and can only be set to boolean (true) or (false). setting this 
    | to true will activate sending to the mock email address, while setting to  
    | false will deactivate sending to mock email address which will in turn   
    | allow for sending to the indicated email address this can be changed at    
    | any point in the life of the of the application but it is recommended 
    | to set it this before deploying.
    |
    */
    'mock_mail' => env('OV_MOCK_MAIL', true),

    /*
    |--------------------------------------------------------------------------
    | Allow User Uploaded Image Files To Be Permanently Deleted From Cloud Or Sever
    |--------------------------------------------------------------------------
    |
    | This value is used when the application needs to determine
    | if to allow specific file delete functions or code blocks to run
    | this is limited to functions that permanently delete the file from server
    | or cloud, please note that value can only be specified in boolean
    | this is also limited to user uploaded files.
    */
    'image_permanent_delete' => false,

    /*
    |--------------------------------------------------------------------------
    | Allow User Uploaded Document Files To Be Permanently Deleted From Cloud Or Sever
    |--------------------------------------------------------------------------
    |
    | This value is used when the application needs to determine
    | if to allow specific file delete functions or code blocks to run
    | this is limited to functions that permanently delete the file from server
    | or cloud, please note that value can only be specified in boolean
    | this is also limited to user uploaded files.
    */
    'document_permanent_delete' => false,
];