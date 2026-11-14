<?php

namespace App\Http\Controllers\ControllersWeb;

use Illuminate\Http\Request;

class homeController extends Controller
{
    public function monitoring()
    {
        return view('monitoring');
    }

    public function home()
    {
        return view('home');
    }

    public function daftar()
    {
        return view('register');
    }
}
