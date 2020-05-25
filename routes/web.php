<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();
/*Route::get('/token', function () {
    return Auth::user()->createToken('test');
});*/

Route::get('/home', 'HomeController@index')->name('home');

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

Route::post('/login/super_clients', 'Auth\LoginController@clientsLogin');
Route::post('/login/animateurs', 'Auth\LoginController@animateursLogin');
Route::post('/register/super_clients', 'Auth\RegisterController@createClient');
Route::post('/register/animateurs', 'Auth\RegisterController@createAnimateur');
Route::post('/message/send_message', 'Auth\MessagesController@sendMessage');
Route::post('/message/send_message_by_users', 'Auth\MessagesController@sendMessageUserByUsers');
Route::get('/animator/register_choose_fack_user', 'Api\AnimatorController@RegisterFackUserChosen');

// Route::view('/home', 'home')->middleware('auth');
Route::view('/super_clients', 'super_clients');
Route::view('/animateurs', 'animateurs');

/*Route::get('testpusher', function () {
    event(new App\Events\ChatEvent('Test notification'));
    return "L'évènement a bien été envoyé!";
});

Route::get('/homepusher', function () {
    return view('testpusher');
});*/
