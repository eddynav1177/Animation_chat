<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Auth;

class ClientsController extends Controller
{
    //
    public function __construct() {

        $this->middleware('guest')->except('logout');
        $this->middleware('guest:super_clients')->except('logout');
        $this->middleware('guest:animateurs')->except('logout');

    }

    public function clientsLoginForm() {

        return view('auth.login', ['url' => 'super_clients']);

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
