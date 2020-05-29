<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Models\ClientsModel;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    | Gestion de l'authentification des clients et des animatrices
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        // $this->middleware('guest:super_clients')->except('logout');
        $this->middleware('guest:animateurs')->except('logout');
    }

    public function clientsLoginForm(Request $request) {

        // return view('auth.login', ['url' => 'super_clients']);
        $login_data = $request->validate([
            'email'     => 'email|required',
            'password'  => 'required',
        ]);

        if (Auth::guard('super_clients')->attempt($login_data)) {
            return response(['message' => 'Invalid login or password']);
        }
        $access_token   = Auth::guard('super_clients')->user()->createToken('authToken')->accessToken;
        return response([
            'user'          => auth()->user(),
            'access_token'  => $access_token
        ]);
    }

    public function clientsLogin(Request $request) {

        $this->validate($request, [
            'email'     => 'required|email',
            'password'  => 'required|min:4',
        ]);

        if (Auth::guard('super_clients')->attempt(['email' => $request->email, "password" => $request->password], $request->get('remember'))) {
            return redirect()->intended('/super_clients');
        }


        return back()->withInput($request->only('email', 'remember'));
    }

    public function animateursLoginForm() {

        return view('auth.login', ['url' => 'animateurs']);

    }

    public function animateursLogin(Request $request) {

        $this->validate($request, [
            'email'     => 'required|email',
            'password'  => 'required|min:4',
        ]);

        if (Auth::guard('animateurs')->attempt(['email' => $request->email, "password" => $request->password], $request->get('remember'))) {
            return redirect()->intended('/animateurs');
        }

        return back()->withInput($request->only('email', 'remember'));
    }
}
