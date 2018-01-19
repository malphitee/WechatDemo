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

Route::any('/', 'MessageController@handler');

Route::any('/test/sendMessage','MessageController@sendText');

Route::any('/test/getMembers','MessageController@getUserList');

Route::any('/test/batchSendMessage','MessageController@batchSendMessage');

Route::any('/test/getMenu','MessageController@getMenu');

Route::any('/test/setMenu','MessageController@setMenu');
