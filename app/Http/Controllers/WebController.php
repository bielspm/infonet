<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class WebController extends Controller
{
    public function showRegister()
    {
        return view('register');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        if (!$token = JWTAuth::attempt($credentials)) {
            return back()->withErrors(['email' => 'Credenciais inválidas.']);
        }
        session(['jwt_token' => $token]);
        return redirect()->route('dashboard');
    }

    public function logout()
    {
        JWTAuth::invalidate(session('jwt_token'));
        session()->forget('jwt_token');
        return redirect()->route('login');
    }
}
