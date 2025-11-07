<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
  use HasFactory;

  protected $fillable = [
    "item_id",
    "notified_at",
    "message",
    "status",
    "role_target",
    "user_id", // Tambahkan jika suatu saat ingin tracking pembuat notifikasi
  ];

  protected $casts = [
    'notified_at' => 'date',
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
}
