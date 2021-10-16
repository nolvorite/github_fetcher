<?php

namespace App\Http\Controllers\Auth;

use \App\Http\Controllers\Controller;
use \App\Providers\RouteServiceProvider;
use \Illuminate\Foundation\Auth\AuthenticatesUsers;
use \Illuminate\Support\Facades\DB;

use \Illuminate\Http\Request;

class LoginController extends Controller
{
	use AuthenticatesUsers;
}