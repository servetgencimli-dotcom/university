<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('portal.dashboard');
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended(route('portal.dashboard'))
                ->with('success', 'Uğurla daxil oldunuz!');
        }

        return back()->withErrors([
            'email' => 'Daxil edilən e-poçt və ya şifrə yanlışdır.',
        ])->onlyInput('email');
    }

    public function showRegister()
    {
        if (Auth::check()) {
            return redirect()->route('portal.dashboard');
        }

        return view('auth.register');
    }

    public function register(RegisterRequest $request)
    {
        $validated = $request->validated();

        $user = new User([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'],
            'role_title' => 'Qəbul Namizədi / Abituriyent',
            'status' => 'active',
        ]);

        $user->role = 'applicant';
        $user->save();

        $applicantRole = Role::where('name', 'applicant')->first();
        if ($applicantRole) {
            $user->roles()->attach($applicantRole);
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('portal.dashboard')
            ->with('success', 'Hesabınız uğurla yaradıldı! Xoş gəlmisiniz.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'Hesabdan çıxış edildi.');
    }
}
