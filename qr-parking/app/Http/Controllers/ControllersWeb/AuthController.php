<?php

// app/Http/Controllers/AuthController.php
namespace App\Http\Controllers\ControllersWeb;

use App\Rules\loginCheck;
use Illuminate\Contracts\Session\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function loginProcess(Request $request)
    {
        $request->validate([
            'username' => 'required|email',
            'password' => ['required', new loginCheck($request)]
        ]);

        $role = session('role');

        // Arahkan berdasarkan role
        if ($role === 'admin_super') {
            return redirect()->route('dashboard'); // contoh: route ke dashboard admin
        } elseif ($role === 'admin') {
            return redirect()->route('dashboard.adminsuper'); // contoh: route ke dashboard admin super
        } else {
            return redirect()->route('home')->withErrors(['role' => 'Role tidak dikenali']);
        }
    }


    public function logout(Request $request)
    {
        session()->flush();

        return redirect()->route('home');
    }
}
