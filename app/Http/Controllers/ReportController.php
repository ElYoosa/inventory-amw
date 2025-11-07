<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OutTransaction;
use App\Models\InTransaction;
use App\Models\Item;
use App\Models\Category;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\OutTransactionExport;
use App\Exports\CategoryReportExport;
use App\Exports\RealtimeReportExport;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use App\Exports\RealtimeExcelExport;

class ReportController extends Controller
{
  public function index(Request $request)
  {
    // --- TAB 1: Distribusi
    $query = OutTransaction::with("item.category")->orderBy("date", "desc");

    if ($request->filled("kode_grup")) {
      $query->where("kode_grup", $request->kode_grup);
    }
    if ($request->filled("date")) {
      $query->whereDate("date", $request->date);
    }

    $reports = $query->paginate(10);
    $kodeGrupList = OutTransaction::whereNotNull("kode_grup")->distinct()->pluck("kode_grup");

    // --- TAB 2: Stok per Kategori
    $categoryReports = Item::with("category")
      ->select("category_id")
      ->selectRaw("COUNT(id) as total_barang")
      ->selectRaw("SUM(stock) as total_stok")
      ->selectRaw("SUM(min_stock) as total_min_stok")
      ->groupBy("category_id")
      ->get();
    $categoriesForCategoryTab = Category::pluck("name", "id");

    // --- TAB 3: Realtime
    $in = InTransaction::with("item.category")
      ->get()
      ->map(function ($m) {
        $m->type = "in";
        return $m;
      });
    $out = OutTransaction::with("item.category")
      ->get()
      ->map(function ($m) {
        $m->type = "out";
        return $m;
      });
    // gunakan concat agar data masuk & keluar tidak saling menimpa
    $transactions = $in->values()->concat($out->values());

    if ($request->filled("start_date")) {
      $transactions = $transactions->filter(fn($t) => $t->date >= $request->start_date);
    }
    if ($request->filled("end_date")) {
      $transactions = $transactions->filter(fn($t) => $t->date <= $request->end_date);
    }
    if ($request->filled("type")) {
      $transactions = $transactions->where("type", $request->type);
    }
    if ($request->filled("category_id")) {
      $catId = (int) $request->category_id;
      $transactions = $transactions->filter(function ($t) use ($catId) {
        return optional(optional($t->item)->category)->id === $catId;
      });
    }

    $transactions = $transactions->sortByDesc("date")->values();
    $categoriesRealtime = Category::pluck("name", "id");

    return view("reports.index", [
      "reports" => $reports,
      "kodeGrupList" => $kodeGrupList,
      "categoryReports" => $categoryReports,
      "categories" => $categoriesForCategoryTab,
      "transactions" => $transactions,
      "categoriesRealtime" => $categoriesRealtime,
    ]);
  }

  // ============================================================
  // 🔸 TAB 1: Distribusi Barang per Keberangkatan (AJAX/Full)
  // ============================================================
  public function distribusi(Request $request)
  {
    // --- TAB 1: Distribusi Barang
    $kodeGrupList = OutTransaction::whereNotNull("kode_grup")->distinct()->pluck("kode_grup");
    $reports = OutTransaction::with("item.category")->orderByDesc("date")->paginate(10);

    // --- TAB 2: Stok per Kategori
    $categoryReports = Item::with("category")
      ->select("category_id")
      ->selectRaw("COUNT(id) as total_barang")
      ->selectRaw("SUM(stock) as total_stok")
      ->selectRaw("SUM(min_stock) as total_min_stok")
      ->groupBy("category_id")
      ->get();
    $categories = Category::pluck("name", "id");

    // --- TAB 3: Realtime Transaction (untuk tab ketiga tetap siap)
    $categoriesRealtime = Category::pluck("name", "id");

    return view("reports.index", [
      "reports" => $reports,
      "kodeGrupList" => $kodeGrupList,
      "categoryReports" => $categoryReports,
      "categories" => $categories,
      "categoriesRealtime" => $categoriesRealtime,
    ]);
  }

  // === Distribusi: Export PDF/Excel ===
  public function exportPDF(Request $request)
  {
    $query = OutTransaction::with("item.category")->orderBy("date", "desc");

    if ($request->filled("kode_grup")) {
      $query->where("kode_grup", $request->kode_grup);
    }
    if ($request->filled("date")) {
      $query->whereDate("date", $request->date);
    }

    $reports = $query->get();

    $pdf = Pdf::loadView("reports.pdf", [
      "reports" => $reports,
      "filter" => ["kode_grup" => $request->kode_grup, "date" => $request->date],
      "generated_at" => now()->format("d/m/Y H:i"),
    ])->setPaper("a4", "landscape");

    return $pdf->download("Laporan_Distribusi_AMW_" . now()->format("Ymd_His") . ".pdf");
  }

  public function exportExcel(Request $request)
  {
    return Excel::download(
      new OutTransactionExport($request->kode_grup, $request->date),
      "Laporan_Distribusi_AMW_" . now()->format("Ymd_His") . ".xlsx",
    );
  }

  // ============================================================
  // 🔸 TAB 2: Laporan Stok per Kategori (AJAX/Full)
  // ============================================================
  public function category(Request $request)
  {
    $categoryReports = Item::with("category")
      ->select("category_id")
      ->selectRaw("COUNT(id) as total_barang")
      ->selectRaw("SUM(stock) as total_stok")
      ->selectRaw("SUM(min_stock) as total_min_stok")
      ->groupBy("category_id")
      ->get();

    $categories = Category::pluck("name", "id");

    if ($request->ajax()) {
      return view("reports.partials.category-report", compact("categoryReports", "categories"));
    }

    return $this->index($request);
  }

  public function exportCategoryPDF()
  {
    // Urutkan sesuai tampilan UI: Kategori (A-Z), lalu Nama Barang (A-Z)
    $data = Item::query()
      ->select("items.*")
      ->with("category")
      ->leftJoin("categories", "categories.id", "=", "items.category_id")
      ->orderBy("categories.name")
      ->orderBy("items.name")
      ->get();
    $pdf = Pdf::loadView("reports.category-pdf", [
      "items" => $data,
      "generated_at" => now()->format("d/m/Y H:i"),
    ])->setPaper("a4", "portrait");

    return $pdf->download("Laporan_Kategori_AMW_" . now()->format("Ymd_His") . ".pdf");
  }

  public function exportCategoryExcel()
  {
    return Excel::download(
      new CategoryReportExport(),
      "Laporan_Kategori_AMW_" . now()->format("Ymd_His") . ".xlsx",
    );
  }

  // ============================================================
  // 🔸 TAB 3: Transaksi Real-Time (AJAX/Full)
  // ============================================================
  public function realtime(Request $request)
  {
    [$transactions, $meta] = $this->getRealtimeDataWithFilters($request);
    $categories = Category::pluck("name", "id");

    if ($request->ajax()) {
      return view("reports.partials.report-realtime", compact("transactions", "categories"));
    }

    return $this->index($request);
  }

  // === Realtime: Export PDF/Excel ===
  public function exportRealtimePDF(Request $request)
  {
    [$transactions, $meta] = $this->getRealtimeDataWithFilters($request);

    $pdf = Pdf::loadView("reports.realtime-pdf", [
      "transactions" => $transactions,
      "filter" => $meta,
      "generated_at" => now()->format("d/m/Y H:i"),
    ])->setPaper("a4", "landscape");

    return $pdf->download("Laporan_Transaksi_RealTime_" . now()->format("Ymd_His") . ".pdf");
  }

  public function exportRealtimeExcel(Request $request)
  {
    [$transactions, $meta] = $this->getRealtimeDataWithFilters($request);

    // gunakan nama file pendek untuk menghindari terpakai sebagai sheet title di runtime
    $shortFilename = "Realtime_" . now()->format("Ymd_His") . ".xlsx"; // { changed code }
    return Excel::download(new RealtimeExcelExport($transactions, $meta), $shortFilename);
  }

  // ============================================================
  // 🔸 UTILITAS INTERNAL: Filter data realtime
  // ============================================================
  private function getRealtimeDataWithFilters(Request $request): array
  {
    $in = InTransaction::with("item.category")
      ->get()
      ->map(fn($m) => tap($m, fn($t) => ($t->type = "in")));
    $out = OutTransaction::with("item.category")
      ->get()
      ->map(fn($m) => tap($m, fn($t) => ($t->type = "out")));
    // concat mencegah overwrite karena key numerik
    $transactions = $in->values()->concat($out->values());

    if ($request->filled("start_date")) {
      $transactions = $transactions->filter(fn($t) => $t->date >= $request->start_date);
    }
    if ($request->filled("end_date")) {
      $transactions = $transactions->filter(fn($t) => $t->date <= $request->end_date);
    }
    if ($request->filled("type")) {
      $transactions = $transactions->where("type", $request->type);
    }
    if ($request->filled("category_id")) {
      $catId = (int) $request->category_id;
      $transactions = $transactions->filter(
        fn($t) => optional(optional($t->item)->category)->id === $catId,
      );
    }

    return [
      $transactions->sortByDesc("date")->values(),
      [
        "start_date" => $request->start_date,
        "end_date" => $request->end_date,
        "type" => $request->type,
        "category_id" => $request->category_id,
      ],
    ];
  }

  // ============================================================
  // 🔸 DATATABLES SERVER SIDE
  // ============================================================
  public function realtimeData(Request $request)
  {
    try {
      // 🔹 Query transaksi masuk
      $in = DB::table("in_transactions as t")
        ->leftJoin("items as i", "i.id", "=", "t.item_id")
        ->leftJoin("categories as c", "c.id", "=", "i.category_id")
        ->select([
          "t.id",
          "t.date",
          DB::raw("'in' as type"),
          "i.id as item_code", // ✅ fix kolom
          "i.name as item_name",
          "c.name as category_name",
          "t.qty",
          "t.sender as partner",
          "t.note",
          "c.id as category_id",
        ]);

      // 🔹 Query transaksi keluar
      $out = DB::table("out_transactions as t")
        ->leftJoin("items as i", "i.id", "=", "t.item_id")
        ->leftJoin("categories as c", "c.id", "=", "i.category_id")
        ->select([
          "t.id",
          "t.date",
          DB::raw("'out' as type"),
          "i.id as item_code", // ✅ fix kolom
          "i.name as item_name",
          "c.name as category_name",
          "t.qty",
          "t.receiver as partner",
          "t.note",
          "c.id as category_id",
        ]);

      // 🔹 Gabungkan dua query
      $union = $in->unionAll($out);
      $q = DB::query()->fromSub($union, "x");

      // 🔹 Terapkan filter (jika ada)
      if ($request->filled("start_date")) {
        $q->whereDate("x.date", ">=", $request->start_date);
      }
      if ($request->filled("end_date")) {
        $q->whereDate("x.date", "<=", $request->end_date);
      }
      if ($request->filled("type")) {
        $q->where("x.type", $request->type);
      }
      if ($request->filled("category_id")) {
        $q->where("x.category_id", (int) $request->category_id);
      }

      // 🔹 Kirim ke DataTables
      return DataTables::of($q->orderByDesc("x.date"))
        ->addIndexColumn()
        ->editColumn(
          "date",
          fn($r) => $r->date ? \Carbon\Carbon::parse($r->date)->format("d M Y") : "-",
        )
        ->editColumn(
          "type",
          fn($r) => $r->type === "in"
            ? '<span class="badge bg-success">Masuk</span>'
            : '<span class="badge bg-danger">Keluar</span>',
        )
        ->editColumn(
          "item_code",
          fn($r) => $r->item_code
            ? '<span class="text-muted">#' . e($r->item_code) . "</span>"
            : "-",
        )
        ->editColumn("qty", fn($r) => '<strong class="text-success">' . e($r->qty) . "</strong>")
        ->rawColumns(["type", "qty", "item_code"])
        ->toJson();
    } catch (\Exception $e) {
      // 🔹 Fallback aman jika error SQL (misal struktur tabel berubah)
      return response()->json([
        "draw" => 0,
        "recordsTotal" => 0,
        "recordsFiltered" => 0,
        "data" => [],
        "error" => "Gagal memuat data realtime: " . $e->getMessage(),
      ]);
    }
  }

  public function distribusiData(Request $request)
  {
    $q = DB::table("out_transactions as ot")
      ->leftJoin("items as i", "i.id", "=", "ot.item_id")
      ->leftJoin("categories as c", "c.id", "=", "i.category_id")
      ->select([
        "ot.id",
        "ot.date",
        "ot.kode_grup",
        "i.id as item_code",
        "i.name as item_name",
        "c.name as category_name",
        "i.unit",
        "ot.qty",
        "ot.receiver",
        "ot.note",
      ])
      ->orderByDesc("ot.date");

    if ($request->filled("date")) {
      $q->whereDate("ot.date", $request->date);
    }
    if ($request->filled("kode_grup")) {
      $q->where("ot.kode_grup", $request->kode_grup);
    }

    return DataTables::of($q)
      ->addIndexColumn()
      ->editColumn(
        "date",
        fn($r) => $r->date ? \Carbon\Carbon::parse($r->date)->format("d M Y") : "-",
      )
      ->editColumn(
        "qty",
        fn($r) => '<strong class="text-success">' . $r->qty . " " . e($r->unit) . "</strong>",
      )
      ->rawColumns(["qty"])
      ->toJson();
  }
}
