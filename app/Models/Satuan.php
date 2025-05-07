<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Satuan extends Model
{
    protected $fillable = [
        'name'
    ];
    public function ItemOrder(){
        return $this->hasMany(ItemOrder::class);
    }
    public function produks()
    {
        return $this->hasMany(Produk::class, 'satuan_id', 'id');
    }
}