<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AnimatorController extends Controller
{
    /*
    AnimatorController: Controleur pour la gestion des animatrices
    */
    //
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function chooseFackUser() {

    }

}
