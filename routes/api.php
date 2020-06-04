<?php

use App\Events\MessagesEvent;
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

/*
AuthController: Controleur pour l'authentification en utilisant passport
UsersController : Gestion des clients et des animatrices après auth
FackUsersController : Controleur pour la gestion des faux utilisateurs incarnés par les animatrices
AnimatorController: Controleur pour la gestion des animatrices
Register Controller : Gestion de l'inscription des clients et des animatrices
ConversationsController: Controleur pour la gestion des conversations entre les clients et les animatrices
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

/*
------------Post------------
*/
// Route::post('/register', 'Api\AuthController@register');
Route::post('/register/user_register', 'Api\AuthController@userRegister');
Route::post('/register/animatrice_register', 'Api\AuthController@animatriceRegister');

Route::post('/login/user_login', 'Api\AuthController@loginUser');
Route::post('/login/animatrice', 'Api\AuthController@loginAnimatrice');

Route::post('/message/chat/{id}', 'Api\MessagesController@sendMessage');
/*
------------End Post------------
*/

/*
------------Get------------
*/
Route::get('/logout/{id}', 'Api\AuthController@loggout');

Route::get('/home/list_users_connected/{id}', 'Api\UsersController@listUsersConnected');
Route::get('/home/list_animator_connected/{id}', 'Api\UsersController@listAnimatorsConnected');

Route::get('/message/view_message/{id}', 'Api\MessagesController@viewMessage');
Route::get('/message/conversations', 'Api\ConversationsController@viewConversations');

Route::get('/user/show_profile/{id}', 'Api\UsersController@showUserProfile');

// Test event
Route::get('/message/check_message/{id}', function () {
    return view('home');
})->middleware('auth');
/*
------------End Get------------
*/

//Route::get('/animator/choose_fack_user/{id}', 'Api\AnimatorController@chooseFackUser');

// Route::view('/home', 'home')->middleware('auth');
/*Route::view('/user', 'user');
Route::view('/animateurs', 'animateurs');*/

