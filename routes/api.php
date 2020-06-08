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

/*
AuthController: Controleur pour l'authentification en utilisant passport
UsersController : Gestion des clients et des animatrices après auth
FackUsersController : Controleur pour la gestion des faux utilisateurs incarnés par les animatrices
AnimatorController : Controleur pour la gestion des animatrices
Register Controller : Gestion de l'inscription des clients et des animatrices
ConversationsController : Controleur pour la gestion des conversations entre les clients et les animatrices
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
// Route::middleware('auth:api')->post ('/broadcast/auth' , 'Api\BroadcastAuthController@auth');

/*
------------Post------------
*/
// Route::post('/register', 'Api\AuthController@register');
// Route::group(['middleware' => 'auth:api'], function() {});
Route::post('/register/user', 'Api\AuthController@userRegister');
Route::post('/register/animator', 'Api\AuthController@animatorRegister');

Route::post('/login/user', 'Api\AuthController@userLogin');
Route::post('/login/animator', 'Api\AuthController@animatorLogin');

Route::post('/message/chat/{id}/{fk_user}', 'Api\MessagesController@sendMessage')->where(['id' => '[0-9]+', 'fk_user' => '[0-9]+']);

Route::post('/fc_user/create/{id}', 'Api\FackUsersController@createFackUser');
Route::post('/fc_user/choose/{id}', 'Api\FackUsersController@chooseFackUserByAdmin');
/*
------------End Post------------
*/

/*
------------Get------------
*/
Route::get('/logout/{id}', 'Api\AuthController@logout');

Route::get('/home/users/list/{id}', 'Api\UsersController@listUsersConnected');
Route::get('/home/animators/list/{id}', 'Api\UsersController@listAnimatorsConnected');

Route::get('/message/view_message/{id}', 'Api\MessagesController@viewMessages');
Route::get('/message/conversations', 'Api\ConversationsController@viewAllConversations');

Route::get('/user/show_profile/{id}', 'Api\UsersController@showUserProfile');

Route::get('/fc_user/list/connected', 'Api\FackUsersController@getFackUsersAllAffected');
Route::get('/fc_user/list/disconnected', 'Api\FackUsersController@getFackUsersAllNotAffected');
Route::get('/fc_user/show_profile/{id}', 'Api\FackUsersController@showProfileFackUser');
// Route::get('/fc_user/conversations/{id}', 'Api\FackUsersController@viewConversationsByUser');

// Test event
Route::get('/message/check_message/{id}/{fk_user}', function () {
    return view('home');
})->middleware('auth');
/*
------------End Get------------
*/


Route::view('/home', 'home')->middleware('auth:api');
/*Route::view('/user', 'user');
Route::view('/animateurs', 'animateurs');*/

