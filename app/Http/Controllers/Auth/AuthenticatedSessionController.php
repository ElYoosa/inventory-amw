<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class AuthenticatedSessionController extends Controller
{
    public function create()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    public function store(Request $request)
    {
        $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        // 🔹 Tentukan login pakai email atau username
        $loginType = (Schema::hasColumn('users', 'username') && !filter_var($request->login, FILTER_VALIDATE_EMAIL))
            ? 'username'
            : 'email';

        $credentials = [
            $loginType => $request->login,
            'password' => $request->password,
        ];

        // 🔹 Pastikan user ada
        $userExists = User::where($loginType, $request->login)->exists();
        if (!$userExists) {
            throw ValidationException::withMessages([
                'login' => __('Akun tidak ditemukan. Periksa kembali username/email Anda.'),
            ]);
        }

        // 🔹 Coba login
        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'password' => __('Password yang Anda masukkan salah.'),
            ]);
        }

        $request->session()->regenerate();

        // 🔸 Catat aktivitas login
        if (Auth::check()) {
            $this->safeLogActivity($request, 'Login');
        }

        // 🔹 Pesan sambutan dinamis
        $role = Auth::user()->role ?? 'user';
        $name = Auth::user()->name ?? 'User';

        $messages = [
            'admin' => [
                'text' => "Selamat datang kembali, <strong>Admin ANAMTA</strong> 👑",
                'color' => '#002B5B',
                'icon'  => '🛠️',
            ],
            'manager' => [
                'text' => "Halo <strong>Manager</strong>! Pantau laporan stok terbaru hari ini 📊",
                'color' => '#0F766E',
                'icon'  => '📈',
            ],
            'staff' => [
                'text' => "Hai <strong>Staff</strong> 👋, segera perbarui transaksi barang masuk dan keluar!",
                'color' => '#CA8A04',
                'icon'  => '📦',
            ],
        ];

        session()->flash('welcome_message', $messages[$role] ?? [
            'text' => "Selamat datang kembali, $name!",
            'color' => '#003B7A',
            'icon'  => '👋',
        ]);

        // 🚀 Redirect otomatis sesuai role
        return $this->redirectAfterLogin();
    }

    public function destroy(Request $request)
    {
        if (Auth::check()) {
            $this->safeLogActivity($request, 'Logout');
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    /**
     * 🚦 Fungsi redirect dinamis setelah login sesuai role
     */
    protected function redirectAfterLogin()
    {
        $role = Auth::user()->role ?? 'staff';

        return match ($role) {
            'admin'   => redirect()->route('dashboard')->with('successToast', 'Selamat datang, Admin!'),
            'manager' => redirect()->route('dashboard')->with('successToast', 'Selamat datang, Manager!'),
            'staff'   => redirect()->route('dashboard')->with('successToast', 'Selamat datang, Staff!'),
            default   => redirect('/login')->with('errorToast', 'Role tidak dikenali.'),
        };
    }

    /**
     * 🧱 Aman: simpan log hanya jika tabel & kolom tersedia
     */
    private function safeLogActivity(Request $request, string $activity): void
    {
        if (!Schema::hasTable('activity_logs')) {
            Log::warning('Tabel activity_logs belum tersedia — log aktivitas dilewati.');
            return;
        }

        $columns = Schema::getColumnListing('activity_logs');
        if (!in_array('username', $columns) || !in_array('role', $columns) || !in_array('activity', $columns)) {
            Log::warning('Struktur tabel activity_logs belum lengkap — log aktivitas dilewati.');
            return;
        }

        [$os, $browser] = $this->getClientInfo($request);

        $data = [
            'username'   => Auth::user()->username ?? 'unknown',
            'role'       => Auth::user()->role ?? 'unknown',
            'activity'   => $activity,
            'ip_address' => $request->ip(),
        ];

        if (in_array('device', $columns)) $data['device'] = $os;
        if (in_array('browser', $columns)) $data['browser'] = $browser;

        try {
            ActivityLog::create($data);
        } catch (\Throwable $e) {
            Log::error('Gagal menyimpan log aktivitas: ' . $e->getMessage());
        }
    }

    /**
     * 🔹 Deteksi OS & browser
     */
    private function getClientInfo(Request $request): array
    {
        $agent = $request->header('User-Agent');
        $os = match (true) {
            preg_match('/Windows/i', $agent) => 'Windows',
            preg_match('/Mac/i', $agent) => 'MacOS',
            preg_match('/Linux/i', $agent) => 'Linux',
            preg_match('/Android/i', $agent) => 'Android',
            preg_match('/iPhone|iPad/i', $agent) => 'iOS',
            default => 'Unknown OS',
        };

        $browser = match (true) {
            preg_match('/Chrome/i', $agent) => 'Chrome',
            preg_match('/Firefox/i', $agent) => 'Firefox',
            preg_match('/Safari/i', $agent) => 'Safari',
            preg_match('/Edge/i', $agent) => 'Edge',
            preg_match('/Opera/i', $agent) => 'Opera',
            default => 'Unknown Browser',
        };

        return [$os, $browser];
    }
}
