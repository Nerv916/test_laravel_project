<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApprovedItems extends Model
{
    protected $fillable = [
        'approved_id',
        'produk_id',
        'qty',
        'satuan',
        'merek',
        'pagu',
        'ppn',
        'harga',
        'harga_setelah_pajak',
        'total_harga_beli',
        'margin',
        'harga_jual_satuan',
        'selisih_pagu',
        'total_harga',
        'status',
    ];

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'produk_id');
    }
    public function barang()
    {
        return $this->belongsTo(Barang::class, 'barang_id', 'id');
    }
    public function approved()
    {
        return $this->belongsTo(Approved::class);
    }
}
