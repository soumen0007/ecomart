<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login()
    {
        return view('pages.login');
    }

    public function signup()
    {
        return view('pages.signup');
    }

    public function loginSubmit(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if ($user && Hash::check($request->password, $user->password_hash)) {
            Auth::login($user);

            return redirect()->route('home');
        }

        return back()
            ->withErrors(['email' => 'Invalid email or password.'])
            ->withInput();
    }

    public function signupSubmit(Request $request)
    {
        $request->validate([
            'first_name' => 'required|max:100',
            'last_name'  => 'required|max:100',
            'email'      => 'required|email|unique:users,email',
            'phone'      => 'required|max:20',
            'password'   => 'required|min:6|confirmed',
        ]);

        User::create([
            'first_name'    => $request->first_name,
            'last_name'     => $request->last_name,
            'email'         => $request->email,
            'phone'         => $request->phone,
            'password_hash' => Hash::make($request->password),
            'created_at'    => now(),
        ]);

        return redirect()
            ->route('login')
            ->with('success', 'Account created successfully. Please login.');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}