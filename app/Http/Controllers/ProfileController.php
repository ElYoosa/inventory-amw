<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /** Tampilkan form profil pengguna */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => Auth::user(),
        ]);
    }

    /** Update informasi profil pengguna */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = Auth::user();
        $validated = $request->validated();

        // Jika tidak ada perubahan data
        if (
            $validated['name'] === $user->name &&
            $validated['email'] === $user->email
        ) {
            return Redirect::route('profile.edit')
                ->with('infoToast', 'Tidak ada perubahan pada profil Anda.');
        }

        // Proses update normal
        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('profile.edit')
            ->with('successToast', 'Profil berhasil diperbarui!');
    }

    /** Hapus akun pengguna */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = Auth::user();

        Auth::logout();
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/')
            ->with('infoToast', 'Akun Anda telah dihapus dari sistem.');
    }
}
