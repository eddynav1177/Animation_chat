<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\ClientsModel;
use App\Models\AnimateursModel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;

class RegisterController extends Controller
{
    //

    public function __construct() {
        $this->middleware('guest');
        $this->middleware('guest:super_clients');
        $this->middleware('guest:animateurs');
    }

    public function clientsRegister() {
        return view('auth.register', ['url', 'super_clients']);
    }

    public function animatorRegister() {
        return view('auth.register', ['url', 'animateurs']);
    }

    // Ajout d'un client
    protected function createClient(Request $request) {
        $this->validator($request->all())->validate();
        $clients = ClientsModel::create([
            'name'      => $request['name'],
            'email'     => $request['email'],
            'password'  => Hash::make($request['password'])
        ]);

        return redirect()->intended('login/super_clients');
    }

    // Ajout d'un animateur
    protected function createAnimateur(Request $request) {

        dd($request);
        die();
        $this->validator($request->all())->validate();
        $clients = AnimateursModel::create([
            'name'      => $request['name'],
            'email'     => $request['email'],
            'password'  => Hash::make($request['password'])
        ]);

        return redirect()->intended('login/animateurs');
    }
}
