<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    /** Tampilkan daftar notifikasi dengan filter status */
    public function index(Request $request)
    {
        $status = $request->get('status'); // ambil filter dari query

        $query = Notification::with('item')
            ->forManager()
            ->latest();

        if ($status && in_array($status, ['new', 'done', 'read'])) {
            $query->where('status', $status);
        }

        $notifications = $query->paginate(10);

        // Jika tidak ada hasil, beri info
        if ($notifications->isEmpty()) {
            session()->flash('infoToast', 'Tidak ada notifikasi untuk filter saat ini.');
        }

        return view('notifications.index', compact('notifications', 'status'));
    }

    /** Tandai semua notifikasi sebagai telah dibaca */
    public function markAllRead()
    {
        $updated = Notification::where('role_target', 'manager')
            ->where('status', 'new')
            ->update(['status' => 'read']);

        if ($updated > 0) {
            return redirect()->route('notifications.index')
                ->with('successToast', "Berhasil menandai {$updated} notifikasi sebagai dibaca.");
        }

        return redirect()->route('notifications.index')
            ->with('infoToast', 'Tidak ada notifikasi baru untuk ditandai.');
    }
}
