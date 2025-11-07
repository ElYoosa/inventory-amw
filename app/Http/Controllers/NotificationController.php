<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
  /** Tampilkan daftar notifikasi dengan filter status & jenis */
  public function index(Request $request)
  {
    // Filter status: new/read/all (default: new)
    $status = $request->get("status", "new");
    // Filter jenis: stock_empty, stock_low, in_transaction, out_transaction, general
    $type = $request->get('type');

    $query = Notification::with("item")
      ->forManager()
      ->recent();

    if ($status && in_array($status, ["new", "read"])) {
      $query->where("status", $status);
    }
    // jika 'all' atau kosong selain daftar di atas, tidak filter status

    if ($type) {
      switch ($type) {
        case 'stock_empty':
          $query->where(function ($q) {
            $q->whereRaw('LOWER(title) LIKE ?', ['%stok habis%'])
              ->orWhereRaw('LOWER(message) LIKE ?', ['%stok%habis%']);
          });
          break;
        case 'stock_low':
          $query->where(function ($q) {
            $q->whereRaw('LOWER(title) LIKE ?', ['%stok menipis%'])
              ->orWhereRaw('LOWER(message) LIKE ?', ['%stok%menipis%']);
          });
          break;
        case 'in_transaction':
          $query->where(function ($q) {
            $q->whereRaw('LOWER(title) LIKE ?', ['%transaksi masuk%'])
              ->orWhereRaw('LOWER(message) LIKE ?', ['%transaksi masuk%']);
          });
          break;
        case 'out_transaction':
          $query->where(function ($q) {
            $q->whereRaw('LOWER(title) LIKE ?', ['%transaksi keluar%'])
              ->orWhereRaw('LOWER(message) LIKE ?', ['%transaksi keluar%']);
          });
          break;
        case 'general':
          // Bukan habis/menipis/masuk/keluar (negative filters)
          $query->where(function ($q) {
            $q->whereRaw("LOWER(COALESCE(title,'')) NOT LIKE '%stok habis%'")
              ->whereRaw("LOWER(COALESCE(message,'')) NOT LIKE '%stok%habis%'")
              ->whereRaw("LOWER(COALESCE(title,'')) NOT LIKE '%stok menipis%'")
              ->whereRaw("LOWER(COALESCE(message,'')) NOT LIKE '%stok%menipis%'")
              ->whereRaw("LOWER(COALESCE(title,'')) NOT LIKE '%transaksi masuk%'")
              ->whereRaw("LOWER(COALESCE(message,'')) NOT LIKE '%transaksi masuk%'")
              ->whereRaw("LOWER(COALESCE(title,'')) NOT LIKE '%transaksi keluar%'")
              ->whereRaw("LOWER(COALESCE(message,'')) NOT LIKE '%transaksi keluar%'");
          });
          break;
        default:
          // unknown type: no-op
          break;
      }
    }

    $notifications = $query->paginate(10)->appends(['status' => $status, 'type' => $type]);

    // Jika tidak ada hasil, beri info
    if ($notifications->isEmpty()) {
      session()->flash("infoToast", "Tidak ada notifikasi untuk filter saat ini.");
    }

    return view("notifications.index", compact("notifications", "status", "type"));
  }

  /** Partial rows + pagination for lightweight auto-refresh */
  public function list(Request $request)
  {
    $status = $request->get('status');
    $type = $request->get('type');
    $page = (int) $request->get('page', 1);

    $query = Notification::with('item')->forManager()->recent();
    if ($status && in_array($status, ['new', 'read'])) {
      $query->where('status', $status);
    }
    if ($type) {
      switch ($type) {
        case 'stock_empty':
          $query->where(function ($q) {
            $q->whereRaw('LOWER(title) LIKE ?', ['%stok habis%'])
              ->orWhereRaw('LOWER(message) LIKE ?', ['%stok%habis%']);
          });
          break;
        case 'stock_low':
          $query->where(function ($q) {
            $q->whereRaw('LOWER(title) LIKE ?', ['%stok menipis%'])
              ->orWhereRaw('LOWER(message) LIKE ?', ['%stok%menipis%']);
          });
          break;
        case 'in_transaction':
          $query->where(function ($q) {
            $q->whereRaw('LOWER(title) LIKE ?', ['%transaksi masuk%'])
              ->orWhereRaw('LOWER(message) LIKE ?', ['%transaksi masuk%']);
          });
          break;
        case 'out_transaction':
          $query->where(function ($q) {
            $q->whereRaw('LOWER(title) LIKE ?', ['%transaksi keluar%'])
              ->orWhereRaw('LOWER(message) LIKE ?', ['%transaksi keluar%']);
          });
          break;
        case 'general':
          $query->where(function ($q) {
            $q->whereRaw("LOWER(COALESCE(title,'')) NOT LIKE '%stok habis%'")
              ->whereRaw("LOWER(COALESCE(message,'')) NOT LIKE '%stok%habis%'")
              ->whereRaw("LOWER(COALESCE(title,'')) NOT LIKE '%stok menipis%'")
              ->whereRaw("LOWER(COALESCE(message,'')) NOT LIKE '%stok%menipis%'")
              ->whereRaw("LOWER(COALESCE(title,'')) NOT LIKE '%transaksi masuk%'")
              ->whereRaw("LOWER(COALESCE(message,'')) NOT LIKE '%transaksi masuk%'")
              ->whereRaw("LOWER(COALESCE(title,'')) NOT LIKE '%transaksi keluar%'")
              ->whereRaw("LOWER(COALESCE(message,'')) NOT LIKE '%transaksi keluar%'");
          });
          break;
      }
    }

    $notifications = $query->paginate(10, ['*'], 'page', $page)->appends(['status' => $status, 'type' => $type]);

    $rows = view('notifications._rows', compact('notifications'))->render();
    $pagination = view('vendor.pagination.simple-bootstrap-5', ['paginator' => $notifications])->render();

    return response()->json([
      'rows' => $rows,
      'pagination' => $pagination,
      'total' => $notifications->total(),
      'count_new' => Notification::forManager()->where('status', 'new')->count(),
    ]);
  }

  /** Hapus satu notifikasi */
  public function destroy(Request $request, Notification $notification)
  {
    if (!in_array($notification->role_target, ['manager', 'all', null], true)) {
      return $request->wantsJson()
        ? response()->json(['error' => 'Notifikasi ini bukan untuk Manager.'], 403)
        : redirect()->route('notifications.index')->with('errorToast', 'Notifikasi ini bukan untuk Manager.');
    }
    $notification->delete();
    if ($request->wantsJson()) {
      $count = Notification::forManager()->where('status', 'new')->count();
      return response()->json(['ok' => true, 'count' => $count]);
    }
    return redirect()->route('notifications.index')->with('successToast', 'Notifikasi dihapus.');
  }

  /** Hapus semua notifikasi (Manager scope) */
  public function destroyAll(Request $request)
  {
    $deleted = Notification::forManager()->delete();
    if ($request->wantsJson()) {
      return response()->json(['deleted' => $deleted, 'count' => 0]);
    }
    return redirect()->route('notifications.index')->with('successToast', 'Semua notifikasi dihapus.');
  }

  /** Hapus semua notifikasi yang sudah dibaca (Manager scope) */
  public function destroyRead(Request $request)
  {
    $deleted = Notification::forManager()->where('status', 'read')->delete();
    if ($request->wantsJson()) {
      $count = Notification::forManager()->where('status', 'new')->count();
      return response()->json(['deleted' => $deleted, 'count' => $count]);
    }
    return redirect()->route('notifications.index')->with('successToast', 'Notifikasi yang sudah dibaca dihapus.');
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
