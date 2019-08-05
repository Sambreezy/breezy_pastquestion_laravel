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

});

Route::group([

    'middleware' => ['AlwaysRespondWithJson','api','VerifyJwtToken'],
    'prefix' => 'v1'

], function ($router) {

    Route::get('index', 'PastQuestionController@index');
    Route::get('index/personal', 'PastQuestionController@personalIndex');
    Route::post('index/multisearch', 'PastQuestionController@multiSearchIndex');
    Route::get('index/singlesearch', 'PastQuestionController@singleSearchIndex');
    Route::post('index/create', 'PastQuestionController@store');
    Route::get('index/show', 'PastQuestionController@show');
    Route::post('index/edit', 'PastQuestionController@update');
    Route::delete('index/delete', 'PastQuestionController@destroy');
    Route::patch('index/restore', 'PastQuestionController@restore');

});
