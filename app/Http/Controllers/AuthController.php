<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\User;
use App\Models\Logs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin() {
        if(Auth::check()) {
            return Inertia::location('/dashboard');
        }

        return Inertia::render('Auth/Login');
    }

    public function login(Request $request) {
        $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required'],
        ]);


        $userDetails = User::where('username', $request->username)->first();

        if(!$userDetails) {
            return back()->withErrors([
                'username' => 'The provided credentials do not match our records.',
            ])->onlyInput('username');
        }

        if(!Hash::check($request->password, $userDetails->password)) {
            return  back()->withErrors([
                'password' => 'Incorrect password.',
            ])->onlyInput('password');
        }

        if($userDetails->status === 'inactive') {
            return back()->withErrors([
                'username' => 'Your account is inactive. Please contact the administrator.',
            ])->onlyInput('username');
        }

        Auth::login($userDetails);
        $userName = $userDetails->name;
        Logs::write('login', 'Authentication', "{$userName} logged in to the system.");
        $request->session()->regenerate();
        return Inertia::location('/dashboard');
    }

    public function logout(Request $request) {
        if (Auth::check()) {
            $userName = Auth::user()->name;
            Logs::write('logout', 'Authentication', "{$userName} logged out of the system.");
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        $request->session()->flash('success', 'Logged out successfully.');
        return Inertia::location('/');
    }

}
