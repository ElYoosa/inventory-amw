<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DefaultUsersSeeder extends Seeder
{
  public function run(): void
  {
    $emailPerusahaan = "umroh.anamta@gmail.com";

    // Hapus semua user lama
    DB::table("users")->truncate();

    // Tambahkan user default
    $users = [
      ["name" => "Admin ANAMTA", "username" => "admin", "role" => "admin"],
      ["name" => "Manager ANAMTA", "username" => "manager", "role" => "manager"],
      ["name" => "Staff ANAMTA", "username" => "staff", "role" => "staff"],
    ];

    foreach ($users as $u) {
      DB::table("users")->insert([
        "name" => $u["name"],
        "username" => $u["username"],
        "email" => $emailPerusahaan,
        "password" => Hash::make("password"),
        "role" => $u["role"],
        "created_at" => now(),
        "updated_at" => now(),
      ]);
    }

    $this->command->info("✅ Akun default berhasil dibuat!");
    $this->command->table(
      ["Role", "Username", "Password", "Email"],
      [
        ["Admin", "admin", "password", $emailPerusahaan],
        ["Manager", "manager", "password", $emailPerusahaan],
        ["Staff", "staff", "password", $emailPerusahaan],
      ],
    );
  }
}
