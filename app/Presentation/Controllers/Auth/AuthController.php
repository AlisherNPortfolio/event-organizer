<?php

namespace App\Presentation\Controllers\Auth;

use App\Presentation\Controllers\Controller;

class AuthController extends Controller
{
    public function __construct()
    {}

    public function register()
    {
        return view('auth.register');
    }
}
