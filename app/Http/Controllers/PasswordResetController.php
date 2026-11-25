<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\View\View;

class PasswordResetController extends Controller
{
    public function requestForm(): View
    {
        return view('auth.forgot-password');
    }

    public function sendLink(Request $request): RedirectResponse
    {
        $validated = $request->validate(['email' => ['required', 'email', 'max:255']]);
        $status = Password::sendResetLink($validated);

        if ($status === Password::RESET_THROTTLED) {
            return back()->withErrors(['email' => 'Permintaan terlalu sering. Coba lagi beberapa saat.']);
        }

        // Pesan dibuat generik agar endpoint tidak membocorkan apakah email terdaftar.
        return back()->with('status', 'Jika email tersebut terdaftar, tautan pengaturan ulang kata sandi akan dikirim.');
    }

    public function resetForm(Request $request, string $token): View
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email'),
        ]);
    }

    public function reset(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', PasswordRule::min(8)],
        ]);

        $status = Password::reset(
            $credentials,
            function (User $user, string $password): void {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->setRememberToken(Str::random(60));

                $user->save();
                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('login')->with('success', 'Kata sandi berhasil diperbarui. Silakan masuk kembali.');
        }

        return back()->withErrors([
            'email' => 'Tautan reset tidak valid atau sudah kedaluwarsa. Minta tautan baru.',
        ]);
    }
}
