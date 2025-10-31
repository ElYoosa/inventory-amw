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
            case 'admin':
                $lowStocks = Item::whereColumn('stock', '<=', 'min_stock')->count();
                if ($lowStocks === 0) {
                    session()->flash('infoToast', 'Semua stok aman! Tidak ada barang menipis hari ini ✅');
                }

                return view('dashboard_admin', [
                    'role'          => 'admin',
                    'userCount'     => User::count(),
                    'totalItems'    => Item::count(),
                    'inCount'       => InTransaction::count(),
                    'outCount'      => OutTransaction::count(),
                    'activityCount' => ActivityLog::count(),
                    'lowStocks'     => Item::whereColumn('stock', '<=', 'min_stock')->get(),
                    'notifications' => Notification::latest()->take(5)->get(),
                ]);

                // 🟩 MANAGER → Monitoring stok
            case 'manager':
                $items = Item::with('category')->get();

                $grouped         = $items->groupBy(fn($i) => optional($i->category)->name ?? 'Tanpa Kategori');
                $chartCategories = $grouped->keys();
                $chartStocks     = $grouped->map(fn($g) => $g->sum('stock'))->values();

                // 🔹 Tambahan Grafik: per kode_grup dan tren mingguan
                $outByGroup = OutTransaction::select('kode_grup', DB::raw('SUM(qty) as total'))
                    ->whereNotNull('kode_grup')
                    ->groupBy('kode_grup')
                    ->orderBy('kode_grup')
                    ->get();

                $outByDate = OutTransaction::select(DB::raw('DATE(date) as date'), DB::raw('SUM(qty) as total'))
                    ->groupBy('date')
                    ->orderBy('date', 'asc')
                    ->limit(7)
                    ->get();

                $groupLabels = $outByGroup->pluck('kode_grup');
                $groupTotals = $outByGroup->pluck('total');
                $dateLabels  = $outByDate->pluck('date')->map(fn($d) => \Carbon\Carbon::parse($d)->format('d M'));
                $dateTotals  = $outByDate->pluck('total');

                if ($items->isEmpty()) {
                    session()->flash('infoToast', 'Belum ada data stok barang yang tersedia.');
                }

                return view('dashboard_manager', [
                    'role'            => 'manager',
                    'items'           => $items,
                    'notifications'   => Notification::latest()->take(10)->get(),
                    'chartCategories' => $chartCategories,
                    'chartStocks'     => $chartStocks,
                    'groupLabels'     => $groupLabels,
                    'groupTotals'     => $groupTotals,
                    'dateLabels'      => $dateLabels,
                    'dateTotals'      => $dateTotals,
                ]);

                // 🟧 STAFF → Transaksi pribadi
            case 'staff':
                $in  = InTransaction::where('user_id', $user->id)->count();
                $out = OutTransaction::where('user_id', $user->id)->count();

                if ($in === 0 && $out === 0) {
                    session()->flash('infoToast', 'Belum ada transaksi yang Anda lakukan.');
                }

                return view('dashboard_staff', [
                    'role'          => 'staff',
                    'inCount'       => $in,
                    'outCount'      => $out,
                    'notifications' => Notification::latest()->take(5)->get(),
                ]);

            default:
                abort(403, 'Akses ditolak. Role pengguna tidak dikenali.');
        }
    }
}
