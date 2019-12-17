<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Laravel CORS
    |--------------------------------------------------------------------------
    |
    | allowedOrigins, allowedHeaders and allowedMethods can be set to array('*')
    | to accept any value.
    |
    | Important : origin access should be restricted after test mode.
    */
   
    'supportsCredentials' => false,
    'allowedOrigins' => ['*'], // ex: www.example.com
    'allowedOriginsPatterns' => [],
    'allowedHeaders' => ['*'], // ex: ['Content-Type', 'X-Requested-With'],
    'allowedMethods' => ['GET','POST','delete'], // ex: ['GET', 'POST', 'PUT',  'DELETE']
    'exposedHeaders' => [],
    'maxAge' => 0,

];
