<?php

namespace App\Observers;

use App\Models\{InTransaction, OutTransaction, Item, Notification};
use Illuminate\Support\Facades\DB;

class TransactionObserver
{
  public function created($trx): void
  {
    DB::transaction(function () use ($trx) {
      // Update stok sesuai tipe transaksi
      if ($trx instanceof InTransaction) {
        $trx->item()->update(["stock" => DB::raw("stock + " . (int) $trx->qty)]);
      } elseif ($trx instanceof OutTransaction) {
        $trx->item()->update(["stock" => DB::raw("stock - " . (int) $trx->qty)]);
      }

      // Ambil ulang data item terkini (lock untuk konsistensi)
      $item = $trx->item()->lockForUpdate()->first();

      // Hitung stok sebelumnya (sebelum transaksi ini diaplikasikan)
      $prevStock = null;
      if ($trx instanceof InTransaction) {
        $prevStock = $item->stock - (int) $trx->qty;
      } elseif ($trx instanceof OutTransaction) {
        $prevStock = $item->stock + (int) $trx->qty;
      }

      // 1) Eskalasi: stok habis (0) → buat/upgrade notifikasi hari ini
      if ($item->stock === 0) {
        $existing = Notification::where("item_id", $item->id)
          ->where("status", "new")
          ->whereDate("notified_at", now()->toDateString())
          ->first();

        $payload = [
          "item_id" => $item->id,
          "notified_at" => now(),
          "message" => "Stok {$item->name} HABIS (0 / batas {$item->min_stock})",
          "status" => "new",
          "role_target" => "manager",
          "title" => "Stok Habis",
        ];

        if ($existing) {
          $existing->update($payload);
        } else {
          Notification::create($payload);
        }
      }

      // 2) Stok menipis (<= min_stock dan > 0) → buat notifikasi (tanpa duplikasi per hari)
      if ($item->stock <= $item->min_stock && $item->stock > 0) {
        $exists = Notification::where("item_id", $item->id)
          ->where("status", "new")
          ->whereDate("notified_at", now()->toDateString())
          ->exists();

        if (!$exists) {
          Notification::create([
            "item_id" => $item->id,
            "notified_at" => now(),
            "message" => "Stok {$item->name} menipis ({$item->stock} / batas {$item->min_stock})",
            "status" => "new",
            "role_target" => "manager",
            "title" => "Stok Menipis",
          ]);
        }
      }

      // 3) Pemulihan: jika sebelumnya <= min_stock dan sekarang >= min_stock → tandai notifikasi sebagai dibaca
      if ($prevStock !== null && $prevStock <= $item->min_stock && $item->stock >= $item->min_stock) {
        Notification::where("item_id", $item->id)
          ->where("status", "new")
          ->update(["status" => "read"]);
      }

      // 4) Notifikasi transaksi (anti-spam via ambang qty)
      try {
        $thIn = (int) config('notifications.transaction_thresholds.in', 10);
        $thOut = (int) config('notifications.transaction_thresholds.out', 10);

        if ($trx instanceof InTransaction && (int) $trx->qty >= $thIn) {
          Notification::create([
            'item_id' => $item->id,
            'notified_at' => now(),
            'message' => "Transaksi masuk {$trx->qty} " . ($item->unit ?? '') . " untuk {$item->name}",
            'status' => 'new',
            'role_target' => 'manager',
            'title' => 'Transaksi Masuk',
          ]);
        }

        if ($trx instanceof OutTransaction && (int) $trx->qty >= $thOut) {
          $to = trim((string) ($trx->receiver ?? ''));
          $extra = $to !== '' ? " ke {$to}" : '';
          Notification::create([
            'item_id' => $item->id,
            'notified_at' => now(),
            'message' => "Transaksi keluar {$trx->qty} " . ($item->unit ?? '') . " untuk {$item->name}{$extra}",
            'status' => 'new',
            'role_target' => 'manager',
            'title' => 'Transaksi Keluar',
          ]);
        }
      } catch (\Throwable $e) {
        // Abaikan kesalahan pembuatan notifikasi agar transaksi utama tetap sukses
      }
    });
  }
}
