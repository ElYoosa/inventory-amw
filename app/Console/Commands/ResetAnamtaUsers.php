<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ResetAnamtaUsers extends Command
{
  protected $signature = "anamta:reset-users";
  protected $description = "Reset semua akun user dan buat ulang akun default admin, manager, dan staff ANAMTA.";

  public function handle(): void
  {
    $emailUtama = "umroh.anamta@gmail.com";

    $this->info("🔄 Menghapus semua akun lama...");

    // Nonaktifkan sementara foreign key check supaya bisa hapus dengan aman
    DB::statement("SET FOREIGN_KEY_CHECKS=0;");
    DB::table("users")->delete();
    DB::statement("ALTER TABLE users AUTO_INCREMENT = 1;");
    DB::statement("SET FOREIGN_KEY_CHECKS=1;");

    // Daftar user default
    $users = [
      ["name" => "Admin ANAMTA", "username" => "admin", "role" => "admin"],
      ["name" => "Manager ANAMTA", "username" => "manager", "role" => "manager"],
      ["name" => "Staff ANAMTA", "username" => "staff", "role" => "staff"],
    ];

    foreach ($users as $u) {
      // Buat email unik yang tetap masuk ke inbox yang sama
      $uniqueEmail = str_replace("@", "+{$u["username"]}@", $emailUtama);

      DB::table("users")->insert([
        "name" => $u["name"],
        "username" => $u["username"],
        "email" => $uniqueEmail,
        "password" => Hash::make("password"),
        "role" => $u["role"],
        "created_at" => now(),
        "updated_at" => now(),
      ]);
    }

    $this->newLine();
    $this->info("✅ Akun default ANAMTA berhasil dibuat ulang!");

    $this->table(
      ["Role", "Username", "Password", "Email"],
      [
        ["Admin", "admin", "password", "umroh.anamta+admin@gmail.com"],
        ["Manager", "manager", "password", "umroh.anamta+manager@gmail.com"],
        ["Staff", "staff", "password", "umroh.anamta+staff@gmail.com"],
      ],
    );

    $this->newLine();
    $this->info("📌 Gunakan perintah ini lagi kapan pun kamu ingin reset akun ANAMTA.");
  }
}
