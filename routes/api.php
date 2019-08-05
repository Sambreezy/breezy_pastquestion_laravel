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
    'prefix' => 'v1/pastquestion'

], function ($router) {

    Route::get('index', 'PastQuestionController@index');
    Route::get('personal', 'PastQuestionController@personalIndex');
    Route::post('multisearch', 'PastQuestionController@multiSearchIndex');
    Route::get('singlesearch', 'PastQuestionController@singleSearchIndex');
    Route::post('create', 'PastQuestionController@store');
    Route::get('show', 'PastQuestionController@show');
    Route::post('edit', 'PastQuestionController@update');
    Route::delete('delete', 'PastQuestionController@destroy');
    Route::patch('restore', 'PastQuestionController@restore');

});
