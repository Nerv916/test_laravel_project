<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Produk extends Model
{
    use HasFactory;

    protected $fillable = [
        'barang_id',
        'merek',
        'kategori_id',
        'supplier_id',
        'satuan_id',
        'nie',
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }


    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'kategori_id', 'id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'id');
    }

    public function satuan()
    {
        return $this->belongsTo(Satuan::class, 'satuan_id', 'id');
    }
    public function costumer()
    {
        return $this->belongsTo(Costumer::class, 'costumer_id');
    }
    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }
    public function stock()
    {
        return $this->hasMany(StockMovement::class, 'produk_id');
    }

    public function getCurrentStockAttribute()
    {
        return $this->stock()->where('type', 'in')->sum('quantity') -
            $this->stock()->where('type', 'out')->sum('quantity');
    }
}
