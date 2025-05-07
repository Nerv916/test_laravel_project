<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Preorder extends Model
{
    use HasFactory;

    protected $fillable = ['parent_id', 'no_spo', 'name_cust', 'no_surat', 'order_date'];

    public function items()
    {
        return $this->hasMany(ItemOrder::class, 'preorder_id', 'id');
    }
    public function getTotalBelanjaAttribute()
    {
        return $this->items()->sum('total_harga');
    }
    public function customer()
    {
        return $this->belongsTo(Costumer::class, 'name_cust', 'id'); // Sesuaikan kolom foreign key
    }
    public function produk()
    {
        return $this->belongsTo(Produk::class, 'produk_id');
    }
    public function approved()
    {
        return $this->hasOne(Approved::class);
    }
    public function parent()
    {
        return $this->belongsTo(Preorder::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Preorder::class, 'parent_id');
    }
    public function details()
    {
        return $this->hasMany(Preorder::class); // atau nama model lo
    }
}
