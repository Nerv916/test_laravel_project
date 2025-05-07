<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    protected $fillable = [
        'nama'
    ];
    public function produks()
    {
        return $this->hasMany(Produk::class);
    }
    // app/Models/Barang.php

    public function kategori()
    {
        return $this->belongsTo(Kategori::class);
    }
}
