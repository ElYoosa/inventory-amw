<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class InventoryDemoSeeder extends Seeder
{
    public function run(): void
    {
        // === USERS ===
        $adminId = DB::table('users')->insertGetId([
            'name' => 'Admin Demo',
            'username' => 'admin_demo',
            'email' => 'admin@anamta.test',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $staffId = DB::table('users')->insertGetId([
            'name' => 'Staff Demo',
            'username' => 'staff_demo',
            'email' => 'staff@anamta.test',
            'password' => Hash::make('password'),
            'role' => 'staff',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $managerId = DB::table('users')->insertGetId([
            'name' => 'Manager Demo',
            'username' => 'manager_demo',
            'email' => 'manager@anamta.test',
            'password' => Hash::make('password'),
            'role' => 'manager',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // === CATEGORIES ===
        $catJamaah = DB::table('categories')->insertGetId([
            'name' => 'Perlengkapan Jamaah',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $catP3K = DB::table('categories')->insertGetId([
            'name' => 'Perlengkapan P3K',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $catATK = DB::table('categories')->insertGetId([
            'name' => 'Perlengkapan ATK',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // === ITEMS (Per kategori) ===
        $items = [
            // --- Perlengkapan Jamaah ---
            $catJamaah => [
                'Suitcase Hardcase',
                'Cabin Hardcase',
                'Passport Bag',
                'Hijab',
                'Prayer Kit (Female)',
                'Money Wallet Big',
                'Neck Pillow',
                'Tumbler'
            ],
            // --- Perlengkapan P3K ---
            $catP3K => [
                'Masker Anti Debu',
                'Panadol Merah',
                'Tolak Angin',
                'Antimo',
                'Koyo',
                'Freshcare',
                'Madu Rasa'
            ],
            // --- Perlengkapan ATK ---
            $catATK => [
                'Pulpen Hitam',
                'Buku Catatan',
                'Map Plastik',
                'Kertas A4',
                'Spidol Permanent',
                'Tinta Printer',
                'Stempel Kantor'
            ],
        ];

        foreach ($items as $catId => $list) {
            foreach ($list as $name) {
                DB::table('items')->insert([
                    'category_id' => $catId,
                    'name' => $name,
                    'unit' => 'pcs',
                    'stock' => rand(10, 50),
                    'min_stock' => rand(3, 10),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $itemIds = DB::table('items')->pluck('id')->toArray();

        // === TRANSAKSI MASUK & KELUAR ===
        foreach (range(1, 20) as $n) {
            $itemId = $itemIds[array_rand($itemIds)];
            $tgl = Carbon::now()->subDays(rand(0, 14));

            DB::table('in_transactions')->insert([
                'item_id' => $itemId,
                'qty' => rand(5, 20),
                'sender' => 'Supplier ' . rand(1, 5),
                'note' => 'Barang masuk batch #' . $n,
                'date' => $tgl,
                'user_id' => $staffId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('out_transactions')->insert([
                'item_id' => $itemId,
                'kode_grup' => 'GRP-' . rand(100, 999),
                'qty' => rand(1, 10),
                'receiver' => 'Jamaah ' . rand(1, 30),
                'note' => 'Distribusi perlengkapan #' . $n,
                'date' => $tgl,
                'user_id' => $adminId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // === NOTIFIKASI MANAGER ===
        $now = Carbon::now();

        DB::table('notifications')->insert([
            [
                'title' => 'Laporan Harian Diperbarui',
                'message' => 'Transaksi keluar terbaru telah ditambahkan ke sistem oleh staf operasional.',
                'role_target' => 'manager',
                'status' => 'new',
                'created_at' => $now->subMinutes(15),
                'updated_at' => $now->subMinutes(15),
            ],
            [
                'title' => 'Stok Barang Mendekati Batas Minimum',
                'message' => 'Kategori Perlengkapan Jamaah memiliki 3 item di bawah stok minimum.',
                'role_target' => 'manager',
                'status' => 'new',
                'created_at' => $now->subHours(1),
                'updated_at' => $now->subHours(1),
            ],
            [
                'title' => 'Distribusi Barang Selesai',
                'message' => 'Barang untuk keberangkatan grup GRP-512 telah selesai dikirim.',
                'role_target' => 'manager',
                'status' => 'read',
                'created_at' => $now->subDay(),
                'updated_at' => $now->subDay(),
            ],
        ]);

        $this->command->info('✅ Inventory Demo Data + NotificationSeeder berhasil dimasukkan.');
    }
}
