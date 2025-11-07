<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\User;
use App\Models\Notification;
use App\Models\ActivityLog;
use App\Models\InTransaction;
use App\Models\OutTransaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
  public function index()
  {
    $user = Auth::user();

    switch ($user->role) {
      // 🟦 ADMIN → Dashboard lengkap
      case "admin":
        $lowStocks = Item::whereColumn("stock", "<=", "min_stock")->count();
        if ($lowStocks === 0) {
          session()->flash("infoToast", "Semua stok aman! Tidak ada barang menipis hari ini ✅");
        }

        return view("dashboard_admin", [
          "role" => "admin",
          "userCount" => User::count(),
          "totalItems" => Item::count(),
          "inCount" => InTransaction::count(),
          "outCount" => OutTransaction::count(),
          "activityCount" => ActivityLog::count(),
          "lowStocks" => Item::whereColumn("stock", "<=", "min_stock")->get(),
          "notifications" => Notification::recent()->take(5)->get(),
        ]);

      // 🟩 MANAGER → Monitoring stok
      case "manager":
        $items = Item::with("category")->get();

        $grouped = $items->groupBy(fn($i) => optional($i->category)->name ?? "Tanpa Kategori");
        $chartCategories = $grouped->keys();
        $chartStocks = $grouped->map(fn($g) => $g->sum("stock"))->values();

        // 🔹 Tambahan Grafik: per kode_grup dan tren mingguan
        $outByGroup = OutTransaction::select("kode_grup", DB::raw("SUM(qty) as total"))
          ->whereNotNull("kode_grup")
          ->groupBy("kode_grup")
          ->orderBy("kode_grup")
          ->get();

        $outByDate = OutTransaction::select(
          DB::raw("DATE(date) as date"),
          DB::raw("SUM(qty) as total"),
        )
          ->groupBy("date")
          ->orderBy("date", "asc")
          ->limit(7)
          ->get();

        $groupLabels = $outByGroup->pluck("kode_grup");
        $groupTotals = $outByGroup->pluck("total");
        $dateLabels = $outByDate
          ->pluck("date")
          ->map(fn($d) => \Carbon\Carbon::parse($d)->format("d M"));
        $dateTotals = $outByDate->pluck("total");

        if ($items->isEmpty()) {
          session()->flash("infoToast", "Belum ada data stok barang yang tersedia.");
        }

        return view("dashboard_manager", [
          "role" => "manager",
          "items" => $items,
          // Hanya notifikasi baru yang ditujukan untuk manager, urut default (notified_at/created_at)
          "notifications" => Notification::forManager()->new()->recent()->take(10)->get(),
          "chartCategories" => $chartCategories,
          "chartStocks" => $chartStocks,
          "groupLabels" => $groupLabels,
          "groupTotals" => $groupTotals,
          "dateLabels" => $dateLabels,
          "dateTotals" => $dateTotals,
        ]);

      // 🟧 STAFF → Transaksi pribadi
      case "staff":
        $inCount = InTransaction::where("user_id", $user->id)->count();
        $outCount = OutTransaction::where("user_id", $user->id)->count();

        if ($inCount === 0 && $outCount === 0) {
          session()->flash("infoToast", "Belum ada transaksi yang Anda lakukan.");
        }

        // Item yang dikelola staf: item yang pernah ditransaksikan oleh staf tsb (in/out)
        $managedItemIds = collect([
          ...InTransaction::where("user_id", $user->id)->pluck("item_id"),
          ...OutTransaction::where("user_id", $user->id)->pluck("item_id"),
        ])
          ->unique()
          ->values();

        // Top 5 stok menipis untuk item yang dikelola staf (urutkan berdasarkan selisih terhadap min_stock)
        $lowStocks = \App\Models\Item::query()
          ->when($managedItemIds->isNotEmpty(), fn($q) => $q->whereIn("id", $managedItemIds))
          ->orderByRaw("(stock - min_stock) ASC")
          ->orderBy("stock")
          ->take(5)
          ->get();

        // Transaksi terbaru (gabungan masuk & keluar), ambil 5 terakhir
        $recentIn = InTransaction::with('item')
          ->where('user_id', $user->id)
          ->orderByDesc('date')
          ->take(5)
          ->get()
          ->map(function ($t) {
            return [
              'type' => 'in',
              'date' => $t->date,
              'qty' => $t->qty,
              'item_name' => optional($t->item)->name ?? '-',
              'note' => $t->note,
            ];
          });

        $recentOut = OutTransaction::with('item')
          ->where('user_id', $user->id)
          ->orderByDesc('date')
          ->take(5)
          ->get()
          ->map(function ($t) {
            return [
              'type' => 'out',
              'date' => $t->date,
              'qty' => $t->qty,
              'item_name' => optional($t->item)->name ?? '-',
              'receiver' => $t->receiver,
              'kode_grup' => $t->kode_grup,
              'note' => $t->note,
            ];
          });

        $recentTransactions = $recentIn
          ->merge($recentOut)
          ->sortByDesc(function ($t) {
            // Normalisasi tanggal untuk perbandingan (string/Carbon)
            try { return \Carbon\Carbon::parse($t['date']); } catch (\Throwable $e) { return now(); }
          })
          ->take(5)
          ->values();

        // Riwayat lengkap ringkas (10 terakhir) untuk tabel di UI
        $historyTransactions = $recentIn
          ->merge($recentOut)
          ->sortByDesc(function ($t) {
            try { return \Carbon\Carbon::parse($t['date']); } catch (\Throwable $e) { return now(); }
          })
          ->take(10)
          ->values();

        return view("dashboard_staff", [
          "role" => "staff",
          "inCount" => $inCount,
          "outCount" => $outCount,
          // Notifikasi hanya yang relevan untuk role staff
          "notifications" => Notification::forRole("staff")->latest()->take(5)->get(),
          // Kirim daftar stok menipis yang relevan dengan staf
          "lowStocks" => $lowStocks,
          // Kirim transaksi terbaru untuk ditampilkan di UI
          "recentTransactions" => $recentTransactions,
          // Kirim riwayat lengkap (10 baris) untuk tabel
          "historyTransactions" => $historyTransactions,
        ]);

      default:
        abort(403, "Akses ditolak. Role pengguna tidak dikenali.");
    }
  }
}
