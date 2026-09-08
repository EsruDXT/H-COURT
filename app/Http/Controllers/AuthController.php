<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Tampilkan halaman login.
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Proses login — dipanggil oleh POST /login (form id="signInForm").
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $remember = $request->boolean('remember');

        if (! Auth::attempt($credentials, $remember)) {
            // Pesan ini otomatis muncul di #emailError karena Blade sudah pakai @error('email')
            return back()
                ->withErrors(['email' => 'Email atau kata sandi yang kamu masukkan salah.'])
                ->onlyInput('email');
        }

        $request->session()->regenerate();

        // TODO: sesuaikan tujuan redirect setelah login berhasil
        return redirect()->intended('/dashboard');
    }

    /**
     * Tampilkan halaman register.
     */
    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * Proses pendaftaran akun baru — dipanggil oleh POST /register (form id="registerForm").
     * Nama field "kelas" / "telepon" berubah sesuai tab peran yang dipilih di register.js,
     * jadi salah satu dari keduanya saja yang benar-benar terkirim (bukan dua-duanya).
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'role' => ['required', 'in:siswa,umum,pengelola'],
            'fullName' => ['required', 'string', 'min:3', 'max:255'],
            'kelas' => ['nullable', 'required_if:role,siswa', 'string', 'max:100'],
            'telepon' => ['nullable', 'required_if:role,umum,pengelola', 'string', 'max:20'],
            'institutionName' => ['nullable', 'required_if:role,pengelola', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'confirmPassword' => ['required', 'same:password'],
            'agreeTerms' => ['accepted'],
        ], [
            'confirmPassword.same' => 'Konfirmasi kata sandi tidak cocok.',
            'email.unique' => 'Email ini sudah terdaftar.',
            'agreeTerms.accepted' => 'Kamu harus menyetujui ketentuan ini.',
        ]);

        $user = User::create([
            'name' => $validated['fullName'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'kelas' => $validated['kelas'] ?? null,
            'telepon' => $validated['telepon'] ?? null,
            'institution_name' => $validated['institutionName'] ?? null,
        ]);

        Auth::login($user);

        // TODO: sesuaikan tujuan redirect setelah daftar berhasil
        return redirect('/dashboard');
    }

    /**
     * Logout.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}