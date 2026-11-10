<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    // ==========================================
    // MENAMPILKAN HALAMAN LOGIN
    // ==========================================

    public function showLogin()
    {
        return view('auth.login');
    }


    // ==========================================
    // PROSES LOGIN
    // ==========================================

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (
            Auth::attempt(
                $credentials,
                $request->boolean('remember')
            )
        ) {

            $request->session()->regenerate();

            return redirect()->intended(route('landing'))
                ->with('success', 'Berhasil login. Selamat datang!');
        }

        return back()
            ->withErrors([
                'email' => 'Email atau kata sandi salah.',
            ])
            ->onlyInput('email');
    }


    // ==========================================
    // MENAMPILKAN HALAMAN REGISTER
    // ==========================================

    public function showRegister()
    {
        return view('auth.register');
    }


    // ==========================================
    // PROSES REGISTER
    // ==========================================

    public function register(Request $request)
    {
        $validated = $request->validate([

            'nama' => ['required', 'string', 'max:255'],

            'role' => ['required', 'in:siswa,umum'],

            'kelas_instansi' => ['required', 'string', 'max:255'],

            'email' => ['required', 'email', 'max:255', 'unique:users,email'],

            'password' => [
                'required',
                'confirmed',
                Password::min(8),
            ],

        ]);

        $user = User::create([

            'name' => $validated['nama'],

            'role' => $validated['role'],

            'kelas_instansi' => $validated['kelas_instansi'],

            'email' => $validated['email'],

            'password' => Hash::make($validated['password']),

        ]);

        // Login otomatis setelah register
        Auth::login($user);

        // Perbarui session
        $request->session()->regenerate();

        // Redirect ke homepage
        return redirect()->route('landing')
            ->with('success', 'Akun berhasil dibuat. Selamat datang!');
    }


    // ==========================================
    // LOGOUT
    // ==========================================

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('landing')
            ->with('success', 'Kamu berhasil logout.');
    }

    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }
}