<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mutasi extends Model
{
    use HasFactory;
    protected $fillable = [
        'kode_produk',
        'user_id',
        'tanggal',
        'jenis_mutasi',
        'jumlah',
    ];
}
