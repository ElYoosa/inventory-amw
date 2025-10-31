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
                $trx->item()->update(['stock' => DB::raw('stock + ' . $trx->qty)]);
            } elseif ($trx instanceof OutTransaction) {
                $trx->item()->update(['stock' => DB::raw('stock - ' . $trx->qty)]);
            }

            // Ambil ulang data item terkini
            $item = $trx->item()->lockForUpdate()->first();

            // Jika stok menipis → buat notifikasi (tanpa duplikasi)
            if ($item->stock <= $item->min_stock) {
                $exists = Notification::where('item_id', $item->id)
                    ->where('status', 'new')
                    ->whereDate('notified_at', now()->toDateString())
                    ->exists();

                if (! $exists) {
                    Notification::create([
                        'item_id'     => $item->id,
                        'notified_at' => now()->toDateString(),
                        'message'     => "⚠️ Stok {$item->name} menipis ({$item->stock} / batas {$item->min_stock})",
                        'status'      => 'new',
                        'role_target' => 'manager',
                    ]);
                }
            }

            // Jika stok kembali normal → tandai notifikasi lama sebagai dibaca
            if ($item->stock > $item->min_stock) {
                Notification::where('item_id', $item->id)
                    ->where('status', 'new')
                    ->update(['status' => 'read']);
            }
        });
    }
}
