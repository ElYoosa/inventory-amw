<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OutTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'user_id',
        'date',
        'kode_grup',
        'qty',
        'receiver',
        'note',
    ];

    protected $casts = [
        'date' => 'date', // { changed code } gunakan 'datetime' jika timestamp lengkap
    ];

    // Relasi ke item (barang)
    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    // Relasi ke user yang melakukan transaksi keluar
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
