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

    // Auth Routes
    Route::post('register', 'AuthController@register');
    Route::post('login', 'AuthController@login');
    Route::post('logout', 'AuthController@logout');
    Route::post('forgot', 'AuthController@forgotPassword');
    Route::post('reset', 'AuthController@resetPassword');
    Route::post('change', 'AuthController@changePassword');
    Route::post('refresh', 'AuthController@refresh');
    Route::post('me', 'AuthController@me');
    Route::get('verify', 'AuthController@verifyEmail');
    Route::get('test', function(){
        return response()->json('See https://documenter.getpostman.com/view/6713287/SzmiXbug',200);
    });
    
    // Socialite Routes
    Route::get('socialite/facebook', 'SocialiteController@redirectToFacebookProvider');
    Route::get('socialite/facebook/callback', 'SocialiteController@handleFacebookProviderCallback');
    Route::get('socialite/linkedin', 'SocialiteController@redirectToLinkedinProvider');
    Route::get('socialite/linkedin/callback', 'SocialiteController@handleLinkedinProviderCallback');
    Route::get('socialite/test', 'SocialiteController@test');
});

Route::group([

    'middleware' => ['AlwaysRespondWithJson','api','VerifyJwtToken'],
    'prefix' => 'v1/user'

], function ($router) {

    // User Routes
    Route::get('index', 'UserController@index');
    Route::post('block', 'UserController@blockUser');
    Route::post('unblock', 'UserController@unBlockUser');
    Route::get('show', 'UserController@show');
    Route::post('edit', 'UserController@update');
    Route::delete('delete', 'UserController@destroy');
    Route::delete('batchdelete', 'UserController@batchDestroy');
    Route::post('restore', 'UserController@restore');
    Route::post('batchrestore', 'UserController@batchRestore');
    Route::delete('permanentdelete', 'UserController@permanentDestroy');
    Route::delete('batchpermanentdelete', 'UserController@batchPermanentDestroy');
    Route::get('test', function(){
        return response()->json('See https://documenter.getpostman.com/view/6713287/SzmiXbug',200);
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
    Route::delete('batchpermanentdelete', 'PastQuestionController@batchPermanentDestroy');
    Route::get('test', function(){
        return response()->json('See https://documenter.getpostman.com/view/6713287/SzmiXbug',200);
    });

});

Route::group([

    'middleware' => ['AlwaysRespondWithJson','api','VerifyJwtToken'],
    'prefix' => 'v1/image'

], function ($router) {

    // Image Routes
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
    Route::delete('batchpermanentdelete', 'ImageController@batchPermanentDestroy');
    Route::get('test', function(){
        return response()->json('See https://documenter.getpostman.com/view/6713287/SzmiXbug',200);
    });

});

Route::group([

    'middleware' => ['AlwaysRespondWithJson','api','VerifyJwtToken'],
    'prefix' => 'v1/document'

], function ($router) {

    // Document Routes
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
    Route::delete('batchpermanentdelete', 'DocumentController@batchPermanentDestroy');
    Route::get('test', function(){
        return response()->json('See https://documenter.getpostman.com/view/6713287/SzmiXbug',200);
    });

});

Route::group([

    'middleware' => ['AlwaysRespondWithJson','api','VerifyJwtToken'],
    'prefix' => 'v1/comment'

], function ($router) {

    // Comment Routes
    Route::get('index', 'CommentController@index');
    Route::get('personal', 'CommentController@personalIndex');
    Route::get('flagged', 'CommentController@flaggedCommentIndex');
    Route::post('create', 'CommentController@store');
    Route::get('show', 'CommentController@show');
    Route::post('edit', 'CommentController@update');
    Route::delete('delete', 'CommentController@destroy');
    Route::delete('batchdelete', 'CommentController@batchDestroy');
    Route::post('restore', 'CommentController@restore');
    Route::post('batchrestore', 'CommentController@batchRestore');
    Route::delete('permanentdelete', 'CommentController@permanentDestroy');
    Route::delete('batchpermanentdelete', 'CommentController@batchPermanentDestroy');
    Route::get('test', function(){
        return response()->json('See https://documenter.getpostman.com/view/6713287/SzmiXbug',200);
    });

});

Route::group([

    'middleware' => ['AlwaysRespondWithJson','api'],
    'prefix' => 'v1/general'

], function ($router) {

    // General Routes
    Route::get('index', 'GeneralController@index');
    Route::post('contactus', 'GeneralController@sendContactUsMessage');
    Route::get('universities/show', 'GeneralController@showUniversities');
    Route::get('universities/clear', 'GeneralController@destroyUniversities');
    Route::get('test', function(){
        return response()->json('See https://documenter.getpostman.com/view/6713287/SzmiXbug',200);
    });

});

Route::group([

    'middleware' => ['AlwaysRespondWithJson','api','VerifyJwtToken'],
    'prefix' => 'v1/social'

], function ($router) {

    // Social Routes
    Route::post('upvote', 'SocialController@upVote');
    Route::post('downvote', 'SocialController@downVote');
    Route::post('flagcomment', 'SocialController@flagComment');
    Route::get('test', function(){
        return response()->json('See https://documenter.getpostman.com/view/6713287/SzmiXbug',200);
    });

});
