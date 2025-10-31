<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Http\RedirectResponse;
use App\Mail\AnamtaPasswordResetMail;
use App\Models\User;

class PasswordResetLinkController extends Controller
{
    public function create()
    {
        return view('auth.forgot-password');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'username' => ['required', 'string', 'exists:users,username'],
        ], [
            'username.exists' => 'Username tidak ditemukan dalam sistem ANAMTA.',
        ]);

        $user = User::where('username', $request->username)->first();
        $emailTujuan = 'umroh.anamta@gmail.com';

        // Buat token reset untuk user terkait
        $token = Password::createToken($user);
        $resetUrl = url(route('password.reset', ['token' => $token, 'email' => $user->email], false));

        // Kirim email custom ANAMTA
        Mail::to($emailTujuan)->send(new AnamtaPasswordResetMail($user->username, $resetUrl));

        return back()->with('status', '✅ Link reset password untuk akun "' . $user->username . '" telah dikirim ke email perusahaan.');
    }
}
