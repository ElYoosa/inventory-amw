<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InTransaction extends Model
{
  use HasFactory;

  protected $fillable = ["item_id", "user_id", "date", "qty", "note"];

  // Relasi ke item (barang)
  public function item()
  {
    return $this->belongsTo(Item::class);
  }

  // Relasi ke user yang melakukan transaksi
  public function user()
  {
    return $this->belongsTo(User::class);
  }
}
