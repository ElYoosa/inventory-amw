<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
  use HasFactory;

  protected $fillable = [
    "item_id",
    "title",
    "notified_at",
    "message",
    "status",
    "role_target",
    "user_id", // Tambahkan jika suatu saat ingin tracking pembuat notifikasi
  ];

  protected $casts = [
    'notified_at' => 'datetime',
  ];

  /** 🔗 Relasi ke item (barang) */
  public function item()
  {
    return $this->belongsTo(Item::class);
  }

  /** 🔗 Relasi opsional ke user (pembuat atau target notifikasi) */
  public function user()
  {
    return $this->belongsTo(User::class);
  }

  /** 📬 Scope: notifikasi baru */
  public function scopeNew($query)
  {
    return $query->where("status", "new");
  }

  /**
   * 🎯 Scope: khusus untuk Manager.
   * Menampilkan notifikasi untuk role manager,
   * atau notifikasi umum (tanpa role_target).
   */
  public function scopeForManager($query)
  {
    return $query->where(function ($q) {
      $q->where("role_target", "manager")
        ->orWhereNull("role_target")
        ->orWhere("role_target", "all"); // jika ada notifikasi umum
    });
  }

  /**
   * 🎯 Scope dinamis untuk role apa pun.
   * Contoh: Notification::forRole(Auth::user()->role)->get()
   */
  public function scopeForRole($query, $role)
  {
    return $query->where(function ($q) use ($role) {
      $q->where("role_target", $role)->orWhereNull("role_target")->orWhere("role_target", "all");
    });
  }

  /** Urutan default: pakai notified_at jika ada, jika tidak pakai created_at */
  public function scopeRecent($query)
  {
    return $query
      ->orderByRaw('COALESCE(notified_at, created_at) DESC')
      ->orderByDesc('created_at');
  }

  // ===== Atribut turunan untuk UI/UX yang lebih informatif =====
  public function getTypeAttribute(): string
  {
    $t = strtolower((string) ($this->title ?? ''));
    $m = strtolower((string) ($this->message ?? ''));

    if (str_contains($t, 'stok habis') || str_contains($m, 'stok') && str_contains($m, 'habis')) {
      return 'stock_empty';
    }
    if (str_contains($t, 'stok menipis') || (str_contains($m, 'stok') && str_contains($m, 'menipis'))) {
      return 'stock_low';
    }
    if (str_contains($t, 'transaksi masuk') || str_contains($m, 'transaksi masuk')) {
      return 'in_transaction';
    }
    if (str_contains($t, 'transaksi keluar') || str_contains($m, 'transaksi keluar')) {
      return 'out_transaction';
    }
    return 'general';
  }

  public function getLevelAttribute(): string
  {
    return match ($this->type) {
      'stock_empty' => 'danger',
      'stock_low' => 'warning',
      'in_transaction' => 'info',
      'out_transaction' => 'info',
      default => 'secondary',
    };
  }

  public function getLabelAttribute(): string
  {
    return match ($this->type) {
      'stock_empty' => 'Habis',
      'stock_low' => 'Menipis',
      'in_transaction' => 'Transaksi Masuk',
      'out_transaction' => 'Transaksi Keluar',
      default => 'Umum',
    };
  }

  public function getIconAttribute(): string
  {
    return match ($this->type) {
      'stock_empty' => 'bi-exclamation-octagon-fill',
      'stock_low' => 'bi-exclamation-triangle-fill',
      'in_transaction' => 'bi-arrow-down-circle-fill',
      'out_transaction' => 'bi-arrow-up-circle-fill',
      default => 'bi-bell-fill',
    };
  }
}
