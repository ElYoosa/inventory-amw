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
          "notified_at" => now()->toDateString(),
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
            "notified_at" => now()->toDateString(),
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
    });
  }
}

