<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'unit',
        'stock',
        'min_stock',
    ];

    // Relasi ke tabel kategori (many-to-one)
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Relasi ke transaksi masuk
    public function inTransactions()
    {
        return $this->hasMany(InTransaction::class);
    }

    // Relasi ke transaksi keluar
    public function outTransactions()
    {
        return $this->hasMany(OutTransaction::class);
    }

    // Relasi ke notifikasi stok minimum
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }
}
