<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    use HasFactory;

    protected $table = 'transaksi';
    protected $guarded = [];
    protected $primaryKey = 'invoice_number';
    public $incrementing = false;
    protected $keyType = 'string';

    public function produk()
    {
        return $this->hasMany(Produk::class, 'invoice_number', 'invoice_number');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
