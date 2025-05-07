<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $fillable = [
        'id_supplier',
        'name',
        'alamat',
        'pic',
        'kontak',
        'npwp',
        'pajak',
    ];
    public static function boot()
    {
        parent::boot();

        static::creating(function ($supplier) {

            $lastId = self::max('id') + 1;
            $supplier->id_supplier = 'SUP-' . str_pad($lastId, 4, '0', STR_PAD_LEFT);
        });
    }
    public function produks()
    {
        return $this->hasMany(Produk::class, 'supplier_id', 'id');
    }
    public function barang()
    {
        return $this->belongsTo(Barang::class, 'barang_id', 'id');
    }
}
