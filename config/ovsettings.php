<?php

return [
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