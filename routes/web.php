<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Default Route
Route::view('/{path?}', 'welcome');

// Route::any('/', function(){
//     return response(
//         'See Documentation At 
//             <a href ="https://documenter.getpostman.com/view/6713287/SVmvUKcu">
//                 https://documenter.getpostman.com/view/6713287/SVmvUKcu
//             </a>'
//     );
// });
