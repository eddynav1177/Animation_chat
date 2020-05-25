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
/*Route::get('/animateurs_login', 'Api\ClientsController@animateursLoginForm')->name('animateurs_login');
Route::get('/client_login', 'Api\ClientsController@clientsLoginForm')->name('client_login');
Route::get('/client_register', 'Api\RegisterController@clientsRegister')->name('client_register');
Route::get('/animateur_register', 'Api\RegisterController@animatorRegister')->name('animateur_register');*/

Route::get('/login/clients', 'Auth\LoginController@clientsLoginForm');
Route::get('/login/animateurs', 'Auth\LoginController@animateursLoginForm');
Route::get('/register/clients', 'Auth\RegisterController@clientsRegister');
Route::get('/register/animateurs', 'Auth\RegisterController@animatorRegister');
Route::get('/message/view', 'Auth\MessagesController@viewMessage');

Route::post('/login/super_clients', 'Auth\LoginController@clientsLogin');
Route::post('/login/animateurs', 'Auth\LoginController@animateursLogin');
Route::post('/register/super_clients', 'Auth\RegisterController@createClient');
Route::post('/register/animateurs', 'Auth\RegisterController@createAnimateur');
Route::post('/message/send_message', 'Auth\MessagesController@sendMessage');

// Route::view('/home', 'home')->middleware('auth');
Route::view('/super_clients', 'super_clients');
Route::view('/animateurs', 'animateurs');

Route::get('testpusher', function () {
    event(new App\Events\ChatEvent('Test notification'));
    return "L'évènement a bien été envoyé!";
});

Route::get('/homepusher', function () {
    return view('testpusher');
});
