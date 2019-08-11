<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group([

    'middleware' => ['AlwaysRespondWithJson','api'],
    'prefix' => 'v1/auth'

], function ($router) {

    Route::post('register', 'AuthController@register');
    Route::post('login', 'AuthController@login');
    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
    Route::post('me', 'AuthController@me');
    Route::get('test', function(){
        return response()->json('See https://github.com/bobbyaxe61',200);
    });

});

Route::group([

    'middleware' => ['AlwaysRespondWithJson','api','VerifyJwtToken'],
    'prefix' => 'v1/pastquestion'

], function ($router) {

    // Past Question Routes
    Route::get('index', 'PastQuestionController@index');
    Route::get('personal', 'PastQuestionController@personalIndex');
    Route::post('multisearch', 'PastQuestionController@multiSearchIndex');
    Route::get('singlesearch', 'PastQuestionController@singleSearchIndex');
    Route::post('create', 'PastQuestionController@store');
    Route::get('show', 'PastQuestionController@show');
    Route::post('edit', 'PastQuestionController@update');
    Route::delete('delete', 'PastQuestionController@destroy');
    Route::delete('batchdelete', 'PastQuestionController@batchDestroy');
    Route::post('restore', 'PastQuestionController@restore');
    Route::post('batchrestore', 'PastQuestionController@batchRestore');
    Route::delete('permanentdelete', 'PastQuestionController@permanentDestroy');
    Route::delete('batchpermanentdelete', 'PastQuestionController@batchpermanentDestroy');
    Route::get('test', function(){
        return response()->json('See https://github.com/bobbyaxe61',200);
    });

});

Route::group([

    'middleware' => ['AlwaysRespondWithJson','api','VerifyJwtToken'],
    'prefix' => 'v1/image'

], function ($router) {

    // Past Question Routes
    Route::get('index', 'ImageController@index');
    Route::get('personal', 'ImageController@personalIndex');
    Route::post('create', 'ImageController@store');
    Route::get('show', 'ImageController@show');
    Route::post('edit', 'ImageController@update');
    Route::delete('delete', 'ImageController@destroy');
    Route::delete('batchdelete', 'ImageController@batchDestroy');
    Route::post('restore', 'ImageController@restore');
    Route::post('batchrestore', 'ImageController@batchRestore');
    Route::delete('permanentdelete', 'ImageController@permanentDestroy');
    Route::delete('batchpermanentdelete', 'ImageController@batchpermanentDestroy');
    Route::get('test', function(){
        return response()->json('See https://github.com/bobbyaxe61',200);
    });

});

Route::group([

    'middleware' => ['AlwaysRespondWithJson','api','VerifyJwtToken'],
    'prefix' => 'v1/document'

], function ($router) {

    // Past Question Routes
    Route::get('index', 'DocumentController@index');
    Route::get('personal', 'DocumentController@personalIndex');
    Route::post('create', 'DocumentController@store');
    Route::get('show', 'DocumentController@show');
    Route::post('edit', 'DocumentController@update');
    Route::delete('delete', 'DocumentController@destroy');
    Route::delete('batchdelete', 'DocumentController@batchDestroy');
    Route::post('restore', 'DocumentController@restore');
    Route::post('batchrestore', 'DocumentController@batchRestore');
    Route::delete('permanentdelete', 'DocumentController@permanentDestroy');
    Route::delete('batchpermanentdelete', 'DocumentController@batchpermanentDestroy');
    Route::get('test', function(){
        return response()->json('See https://github.com/bobbyaxe61',200);
    });

});
