<?php

namespace App\Http\Controllers\Auth;

use App\Models\ClientsModel;
use App\Models\AnimateursModel;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
      Gestion de l'inscription des clients et des animatrices
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
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
        $this->middleware('guest');
        $this->middleware('guest:super_clients');
        $this->middleware('guest:animateurs');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }

    public function clientsRegister() {
        return view('auth.register', ['url' => 'super_clients']);
    }

    public function animatorRegister() {
        return view('auth.register', ['url'=> 'animateurs']);
    }

    // Ajout d'un client
    protected function createClient(Request $request) {
        $this->validator($request->all())->validate();
        $clients = ClientsModel::create([
            'name'      => $request['name'],
            'email'     => $request['email'],
            'password'  => Hash::make($request['password'])
        ]);

        return redirect()->intended('login/clients');
    }

    // Ajout d'un animateur
    protected function createAnimateur(Request $request) {

        $this->validator($request->all())->validate();
        $clients = AnimateursModel::create([
            'name'      => $request['name'],
            'email'     => $request['email'],
            'password'  => Hash::make($request['password'])
        ]);

        return redirect()->intended('login/animateurs');
    }
}
