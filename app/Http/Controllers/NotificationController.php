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
    // Filter status: new/read/all (default: new)
    $status = $request->get("status", "new");

    $query = Notification::with("item")
      ->forManager()
      ->orderByDesc("notified_at")
      ->orderByDesc("created_at");

    if ($status && in_array($status, ["new", "read"])) {
      $query->where("status", $status);
    }
    // jika 'all' atau kosong selain daftar di atas, tidak filter status

    $notifications = $query->paginate(10);

    // Jika tidak ada hasil, beri info
    if ($notifications->isEmpty()) {
      session()->flash("infoToast", "Tidak ada notifikasi untuk filter saat ini.");
    }

    return view("notifications.index", compact("notifications", "status"));
  }

  /** Tandai semua notifikasi sebagai telah dibaca */
  public function markAllRead(Request $request)
  {
    $updated = Notification::forManager()
      ->where("status", "new")
      ->update(["status" => "read"]);

    if ($request->wantsJson()) {
      $count = Notification::forManager()->where("status", "new")->count();
      return response()->json([
        "updated" => $updated,
        "count" => $count,
        "message" => $updated > 0
          ? "Berhasil menandai {$updated} notifikasi sebagai dibaca."
          : "Tidak ada notifikasi baru untuk ditandai.",
      ]);
    }

    if ($updated > 0) {
      return redirect()
        ->route("notifications.index")
        ->with("successToast", "Berhasil menandai {$updated} notifikasi sebagai dibaca.");
    }

    return redirect()
      ->route("notifications.index")
      ->with("infoToast", "Tidak ada notifikasi baru untuk ditandai.");
  }

  /** Tandai satu notifikasi sebagai telah dibaca */
  public function markRead(Request $request, Notification $notification)
  {
    // Pastikan notifikasi ini memang untuk manager (atau umum)
    if (!in_array($notification->role_target, ['manager', 'all', null], true)) {
      if ($request->wantsJson()) {
        return response()->json(["error" => "Notifikasi ini bukan untuk Manager."], 403);
      }
      return redirect()->route('notifications.index')->with('errorToast', 'Notifikasi ini bukan untuk Manager.');
    }

    if ($notification->status === 'new') {
      $notification->update(['status' => 'read']);
      if ($request->wantsJson()) {
        $count = Notification::forManager()->where('status', 'new')->count();
        return response()->json(["ok" => true, "id" => $notification->id, "count" => $count]);
      }
      return redirect()->route('notifications.index')->with('successToast', 'Notifikasi ditandai sebagai dibaca.');
    }

    if ($request->wantsJson()) {
      $count = Notification::forManager()->where('status', 'new')->count();
      return response()->json(["ok" => true, "id" => $notification->id, "count" => $count]);
    }
    return redirect()->route('notifications.index')->with('infoToast', 'Notifikasi sudah dalam status dibaca.');
  }

  /** Hitung jumlah notifikasi baru untuk manager (AJAX) */
  public function count()
  {
    $count = Notification::forManager()->where('status', 'new')->count();
    return response()->json(["count" => $count]);
  }
}
