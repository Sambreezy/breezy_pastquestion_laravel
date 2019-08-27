<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    // Admin Level Token
    protected $USER_LEVEL_3 = 'SuPeRuPeraDmIn';
    protected $USER_LEVEL_2 = 'TeMpOrAlAdMins';
    protected $USER_LEVEL_1 = 'user';

    // HTTP status codes
    static $HTTP_NOT_FOUND = 404;
    static $HTTP_OK = 200;
    static $HTTP_UNPROCESSABLE_ENTITY = 422;
    static $HTTP_UNAUTHORIZED = 401;
    static $HTTP_BAD_REQUEST = 400;
    static $HTTP_CONFLICT= 409;
    static $HTTP_FORBIDDEN = 403;
    static $FAILED = 'failed';
    static $SUCCESS = 'success';
 
    /**
     * Returns a json when data is not found
     *
     * @param string $message
     * @param string $redirect
     * @return json
     */
    public function notFound($message, $redirect=''){
        $info = [
            "status" => self::$FAILED,
            "status_code" => self::$HTTP_NOT_FOUND,
            "message" => $message,
            "redirect" => $redirect,
        ];
        return response()->json($info,self::$HTTP_NOT_FOUND);
    }
 
 
    /**
     * Executes and returns well formatted json of errors 
     * that occured during validation
     *
     * @param string $message
     * @param collection $errors
     * @return json
     */
    public function validationFailed($message, $errors){
        
        $info = [
            'status' => self::$FAILED,
            'errors' => $errors, 
            'status_code' => self::$HTTP_UNPROCESSABLE_ENTITY, 
            "message" => $message,
        ];
        return response()->json($info,self::$HTTP_UNPROCESSABLE_ENTITY);
    }
 
 
    /**
     * Returns json stating why a request is unauthorized
     * 
     * @param string $message
     * @param string $redirect
     * @return json
     */
    public function unauthorized($message, $redirect=''){
        $info = [
            "status" => self::$FAILED,
            "status_code" => self::$HTTP_UNAUTHORIZED,
            "message" => $message,
            "redirect" => $redirect,
        ];
        return response()->json($info,self::$HTTP_UNAUTHORIZED);
    }

    /**
     * Returns json stating process success
     * 
     * @param string $data
     * @return json
     */
    public function success($data){
        $info = [
            "data" => $data,
            "status" => self::$SUCCESS,
            "status_code" => self::$HTTP_OK,
            "message" => "successful",
        ];
        return response()->json($info,self::$HTTP_OK);
    }

    /**
     * Returns json stating why data creation succeeded
     * 
     * @param string $message
     * @param string $redirect
     * @return json
     */
    public function actionSuccess($message, $redirect=''){
        $info = [
            "status" => self::$SUCCESS,
            "status_code" => self::$HTTP_OK,
            "message" => $message,
            "redirect" => $redirect,
        ];
        return response()->json($info,self::$HTTP_OK);
    }
 
    /**
     * Returns json stating process failure due to improper request
     * 
     * @param string $message
     * @param string $redirect
     * @return json
     */
    public function failure($message, $redirect=''){
        $info = [
            "status" => self::$FAILED,
            "status_code" => self::$HTTP_BAD_REQUEST,
            "message" => $message,
            "redirect" => $redirect,
        ];
        return response()->json($info,self::$HTTP_BAD_REQUEST);
    }

    /**
     * Returns json stating why process failed
     * 
     * @param string $message
     * @param string $redirect
     * @return json
     */
    public function actionFailure($message, $redirect=''){
        $info = [
            "status" => self::$FAILED,
            "status_code" => self::$HTTP_CONFLICT,
            "message" => $message,
            "redirect" => $redirect,
        ];
        return response()->json($info,self::$HTTP_CONFLICT);
    }

    /**
     * Returns json stating why process is not permitted
     * 
     * @param string $message
     * @param string $redirect
     * @return json
     */
    public function forbidden($message, $redirect=''){
        $info = [
            "status" => self::$FAILED,
            "status_code" => self::$HTTP_FORBIDDEN,
            "message" => $message,
            "redirect" => $redirect,
        ];
        return response()->json($info,self::$HTTP_FORBIDDEN);
    }
}
