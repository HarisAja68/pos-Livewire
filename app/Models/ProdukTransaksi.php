<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProdukTransaksi extends Model
{
    use HasFactory;

    protected $table = 'produk_transaksi';
    protected $guarded = [];

    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }
}
