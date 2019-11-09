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
    'email_verification' => true,

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
    'email_verification_token_key' => 'Tw1L5DkA0fpg6Iuc2JKpHPANfuKCnlVs',

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