<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/register', 'Api\AuthController@register');
Route::post('/login', 'Api\AuthController@login');




Route::get('/login/clients', 'Auth\LoginController@clientsLoginForm');
Route::get('/login/animateurs', 'Auth\LoginController@animateursLoginForm');
Route::get('/register/clients', 'Auth\RegisterController@clientsRegister');
Route::get('/register/animateurs', 'Auth\RegisterController@animatorRegister');
Route::get('/message/view', 'Api\MessagesController@viewMessage');
Route::get('/home/show_profile_client', 'Api\ClientsController@showProfileUser');
Route::get('/home/list_users_connected', 'Api\ClientsController@listUsersConnected');
Route::get('/home/list_animator_connected', 'Api\AnimatorController@listAnimatorsConnected');
Route::get('/home/show_profile_animator', 'Api\AnimatorController@showProfileAnimator');
Route::get('/message/chat_user', 'Api\ClientsController@chatByUser');
Route::get('/animator/change_animator', 'Api\AnimatorController@changeAnimatorIfNotPresent');
Route::get('/fack_users/show_profile', 'Api\FackUsersController@showProfile');
Route::get('/animator/choose_fack_user', 'Api\AnimatorController@chooseFackUser');

Route::post('/login/user', 'Auth\LoginController@clientsLogin');
Route::post('/login/animateurs', 'Auth\LoginController@animateursLogin');
Route::post('/register/user', 'Auth\RegisterController@createClient');
Route::post('/register/animateurs', 'Auth\RegisterController@createAnimateur');
Route::post('/message/send_message', 'Api\MessagesController@sendMessage');
Route::post('/message/send_message_by_users', 'Api\MessagesController@sendMessageUserByUsers');
Route::get('/animator/register_choose_fack_user', 'Api\AnimatorController@RegisterFackUserChosen');

// Route::view('/home', 'home')->middleware('auth');
Route::view('/user', 'user');
Route::view('/animateurs', 'animateurs');
