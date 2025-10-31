<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Notification;
use Carbon\Carbon;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        Notification::truncate();

        $now = Carbon::now();

        Notification::insert([
            [
                'item_id' => 1,
                'message' => 'Stok barang “Masker Medis” menipis!',
                'status' => 'new',
                'role_target' => 'manager',
                'notified_at' => $now->subMinutes(10),
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'item_id' => 2,
                'message' => 'Barang “Thermometer” telah masuk ke gudang pusat.',
                'status' => 'read',
                'role_target' => 'all',
                'notified_at' => $now->subHour(),
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'item_id' => 3,
                'message' => 'Stok “Sarung Tangan Latex” hanya tersisa 5 pcs!',
                'status' => 'new',
                'role_target' => 'manager',
                'notified_at' => $now->subMinutes(3),
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
