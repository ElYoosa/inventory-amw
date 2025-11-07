<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;

class ValidateRouteMiddleware extends Command
{
  /**
   * Nama dan signature command.
   */
  protected $signature = "amw:validate-middleware";

  /**
   * Deskripsi command.
   */
  protected $description = "Validasi apakah semua route memiliki middleware auth dan role yang sesuai (admin, manager, staff).";

  /**
   * Jalankan command.
   */
  public function handle()
  {
    $this->info("🔍 Memeriksa semua route Laravel...\n");

    $routes = Route::getRoutes();
    $invalid = [];

    foreach ($routes as $route) {
      $uri = $route->uri();
      $middlewares = $route->gatherMiddleware();

      // Hanya periksa route aplikasi (bukan auth bawaan Breeze / Sanctum)
      if (
        !str_starts_with($uri, "sanctum") &&
        !str_starts_with($uri, "login") &&
        !str_starts_with($uri, "register")
      ) {
        if (
          !in_array("auth", $middlewares) ||
          !collect($middlewares)->contains(fn($m) => str_starts_with($m, "role"))
        ) {
          $invalid[] = [
            "uri" => $uri,
            "name" => $route->getName() ?? "-",
            "middleware" => implode(", ", $middlewares),
          ];
        }
      }
    }

    if (empty($invalid)) {
      $this->newLine();
      $this->line(
        "✅ <fg=green>Semua route sudah memiliki middleware yang sesuai (auth & role).</>",
      );
    } else {
      $this->newLine();
      $this->line("⚠️ <fg=yellow>Ditemukan route yang belum memiliki middleware lengkap:</>\n");
      $this->table(["URI", "Nama Route", "Middleware Saat Ini"], $invalid);
    }

    $this->newLine(2);
    $this->line("📋 <fg=cyan>Daftar semua route terdeteksi:</>\n");

    $this->table(
      ["URI", "Nama Route", "Middleware"],
      collect(Route::getRoutes())
        ->map(
          fn($r) => [
            "URI" => $r->uri(),
            "Nama Route" => $r->getName() ?? "-",
            "Middleware" => implode(", ", $r->gatherMiddleware()),
          ],
        )
        ->toArray(),
    );

    $this->newLine();
    $this->info("✅ Pemeriksaan selesai.");
    return Command::SUCCESS;
  }
}
