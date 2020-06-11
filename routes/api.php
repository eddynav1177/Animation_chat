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
RegisterController : Gestion de l'inscription des clients et des animatrices
ConversationsController : Controleur pour la gestion des conversations entre les clients et les animatrices
*/
Route::group(['middleware' => 'auth:api'], function() {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    // Route::get('/logout', 'Api\AuthController@logout');
    Route::view('/home', 'home');
});
Route::get('/logout', 'Api\AuthController@logout');

Route::post('/register/user', 'Api\RegisterController@userRegister');
Route::post('/register/animator', 'Api\RegisterController@animatorRegister');
Route::post('/login/user', 'Api\AuthController@loginAll');

Route::group(['middleware' => 'auth'], function() {
    Route::post('/message/tchat/{id}/{fk_user}', 'Api\MessagesController@sendMessage');
    Route::post('/fk_user/create', 'Api\FackUsersController@createFakeUser');

    Route::get('/users/list', 'Api\UsersController@listUsersConnected');
    Route::get('/user/show_profile/{id}', 'Api\UsersController@showUserProfile');
    Route::get('/animators/list', 'Api\UsersController@listAnimatorsConnected');

    Route::get('/message/view_messages/{id}/{fk_user}/{user_id}', 'Api\MessagesController@viewMessages');
    Route::get('/conversations/list', 'Api\ConversationsController@viewAllConversations');

    Route::get('/fk_user/list', 'Api\FackUsersController@fakeUsersList');
    Route::get('/fk_user/show_profile/{id}', 'Api\FackUsersController@showProfileFakeUser');

    // Test event
    Route::get('/message/check_message/{id}/{fk_user}/{user_id}', function () {
        return view('home');
    });
});
