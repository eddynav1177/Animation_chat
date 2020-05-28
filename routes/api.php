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
FackUsersController : controleur pour la gestion des faux utilisateurs incarnés par les animatrices
AnimatorController: Controleur pour la gestion des animatrices
Register Controller : Gestion de l'inscription des clients et des animatrices
ClientsController: Controleur pour la gestion des clients
ConversationsController: Controleur pour la gestion conversations entre les clients et les animatrices
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// Route::post('/register', 'Api\AuthController@register');
Route::post('/register/user_register', 'Api\AuthController@userRegister');
Route::post('/register/animatrice_register', 'Api\AuthController@animatriceRegister');
Route::post('/login/user_login', 'Api\AuthController@loginUser');
Route::post('/login/animatrice', 'Api\AuthController@loginAnimatrice');
Route::get('/logout/{id}', 'Api\AuthController@loggout');
// Route::post('/redirect/home', 'Api\AuthController@login');

Route::get('/home/list_users_connected/{id}', 'Api\UsersController@listUsersConnected');
Route::get('/home/list_animator_connected/{id}', 'Api\UsersController@listAnimatorsConnected');

Route::post('/message/chat/{id}', 'Api\MessagesController@sendMessage');
// Route::get('/message/animatrice_messages/{id}', 'Api\MessagesController@viewAnimatriceMessages');
Route::get('/message/view_message/{id}', 'Api\MessagesController@viewMessage');
Route::get('/message/status_message/{id}', 'Api\MessagesController@verificationMessagesStatusByUsers');

Route::get('/conversation/{id}', 'Api\ConversationsController@viewConversation');
Route::get('/conversation/register', 'Api\ConversationsController@registerConversation');

/*Route::post('/message/send_message', 'Api\MessagesController@sendMessage');
Route::post('/message/send_message_by_users', 'Api\MessagesController@sendMessageUserByUsers');*/

Route::post('/animator/register_choose_fack_user', 'Api\AnimatorController@RegisterFackUserChosen');


Route::get('/send', function () {
    return event(new MessagesEvent('test'));
});

Route::get('/animator/change_animator', 'Api\AnimatorController@changeAnimatorIfNotPresent');
Route::get('/animator/choose_fack_user/{id}', 'Api\AnimatorController@chooseFackUser');

// Route::get('/fack_users/show_profile/{id}', 'Api\FackUsersController@showProfile');
/*Route::get('/home/show_profile_client/{id}', 'Api\ClientsController@showProfileUser');
Route::get('/home/show_profile_animator/{id}', 'Api\AnimatorController@showProfileAnimator');*/

// Route::view('/home', 'home')->middleware('auth');
Route::view('/user', 'user');
Route::view('/animateurs', 'animateurs');
