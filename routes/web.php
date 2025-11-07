<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
  ProfileController,
  DashboardController,
  CategoryController,
  ItemController,
  InTransactionController,
  OutTransactionController,
  NotificationController,
  ReportController,
  ActivityLogController,
};

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
| Seluruh route sistem Inventory Perlengkapan Operasional
| PT Annur Maknah Wisata (ANAMTA)
|--------------------------------------------------------------------------
*/

// 🔰 Root otomatis redirect ke dashboard
Route::get("/", fn() => redirect()->route("dashboard"));

// 🔒 Semua route berikut membutuhkan login
Route::middleware(["auth"])->group(function () {
  // 🏠 DASHBOARD — bisa diakses semua role
  Route::get("/dashboard", [DashboardController::class, "index"])->name("dashboard");

  /*
    |--------------------------------------------------------------------------
    | 🔹 ADMIN SECTION
    |--------------------------------------------------------------------------
    | Admin dapat mengelola data master dan melihat aktivitas sistem.
    */
  Route::middleware(["role:admin"])->group(function () {
    Route::resources([
      "categories" => CategoryController::class,
      "items" => ItemController::class,
    ]);

    Route::get("/activity-log", [ActivityLogController::class, "index"])->name("activity.index");
  });

  /*
    |--------------------------------------------------------------------------
    | 🔹 STAFF & ADMIN SECTION
    |--------------------------------------------------------------------------
    | Admin dan Staff dapat melakukan transaksi barang masuk & keluar.
    */
  Route::middleware(["role:admin,staff"])->group(function () {
    Route::resources([
      "in-transactions" => InTransactionController::class,
      "out-transactions" => OutTransactionController::class,
    ]);
  });

  /*
    |--------------------------------------------------------------------------
    | 🔹 MANAGER SECTION
    |--------------------------------------------------------------------------
    | Manager dapat mengakses notifikasi & laporan (read-only)
    */
  Route::middleware(["role:manager"])->group(function () {
    // 🔔 NOTIFIKASI
    Route::get("/notifications", [NotificationController::class, "index"])->name("notifications.index");
    Route::post("/notifications/mark-all-read", [
      NotificationController::class,
      "markAllRead",
    ])->name("notifications.markAllRead");
    Route::post('/notifications/{notification}/mark-read', [
      NotificationController::class,
      'markRead',
    ])->name('notifications.markRead');
    Route::get('/notifications/count', [
      NotificationController::class,
      'count',
    ])->name('notifications.count');
    // Partial list + delete actions
    Route::get('/notifications/list', [
      NotificationController::class,
      'list',
    ])->name('notifications.list');
    Route::delete('/notifications/{notification}', [
      NotificationController::class,
      'destroy',
    ])->name('notifications.destroy');
    Route::delete('/notifications', [
      NotificationController::class,
      'destroyAll',
    ])->name('notifications.destroyAll');
    Route::delete('/notifications/read', [
      NotificationController::class,
      'destroyRead',
    ])->name('notifications.destroyRead');

    /*
        |--------------------------------------------------------------------------
        | 📊 REPORTS (LAPORAN)
        |--------------------------------------------------------------------------
        | Semua laporan (Distribusi, Kategori, Realtime) berada di bawah prefix /reports
        | Tambahkan route index agar tombol “Lihat Laporan” tidak error.
        |--------------------------------------------------------------------------
        */
    Route::prefix("reports")
      ->name("reports.")
      ->group(function () {
        // 🔸 ROUTE INDEX UTAMA (Agar dashboard tidak error)
        Route::get("/", [ReportController::class, "index"])->name("index");

        // TAB 1: Distribusi Barang per Keberangkatan
        Route::prefix("distribusi")
          ->name("distribusi.")
          ->group(function () {
            Route::get("/", [ReportController::class, "distribusi"])->name("index");
            Route::get("/data", [ReportController::class, "distribusiData"])->name("data");
            Route::get("/pdf", [ReportController::class, "exportPDF"])->name("pdf");
            Route::get("/excel", [ReportController::class, "exportExcel"])->name("excel");
          });

        // TAB 2: Stok per Kategori
        Route::prefix("category")
          ->name("category.")
          ->group(function () {
            Route::get("/", [ReportController::class, "category"])->name("index");
            Route::get("/pdf", [ReportController::class, "exportCategoryPDF"])->name("pdf");
            Route::get("/excel", [ReportController::class, "exportCategoryExcel"])->name("excel");
          });

        // TAB 3: Transaksi Realtime
        Route::prefix("realtime")
          ->name("realtime.")
          ->group(function () {
            Route::get("/", [ReportController::class, "realtime"])->name("index");
            Route::get("/data", [ReportController::class, "realtimeData"])->name("data");
            Route::get("/pdf", [ReportController::class, "exportRealtimePDF"])->name("pdf");
            Route::get("/excel", [ReportController::class, "exportRealtimeExcel"])->name("excel");
          });
      });
  });

  /*
    |--------------------------------------------------------------------------
    | 👤 PROFILE SECTION
    |--------------------------------------------------------------------------
    */
  Route::get("/profile", [ProfileController::class, "edit"])->name("profile.edit");
  Route::patch("/profile", [ProfileController::class, "update"])->name("profile.update");
  Route::delete("/profile", [ProfileController::class, "destroy"])->name("profile.destroy");
});

// 🔑 AUTENTIKASI (Laravel Breeze)
require __DIR__ . "/auth.php";
