<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{

    public function __construct()
    {
        $this->middleware('guest', ['except' => 'logout']);
    }


    public function index()
    {        
        return view('admin.login');
    }

    public function login_store(Request $request)
    {
        // dd($request);
        $credentials = $request->validate([
            'name' => 'required',
            'password'=> 'required|min:6|'
        ]);

        if (Auth::attempt($credentials)) {
            return redirect('/transaction')->with('success', 'Login Berhasil');
        }

        return redirect('/login')->with('failed', 'Username atau password anda salah');
    }

    public function logout(Request $request)
    {
        // dd($request);
        Auth::logout();
        return redirect('/login');
    }
}
