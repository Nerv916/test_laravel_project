<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ItemOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'preorder_id',
        'produk_id',
        'qty',
        'pagu',
        'total_pagu',
        'harga',
        'total_harga',
        'margin',
        'status'
    ];

    public function preorder()
    {
        return $this->belongsTo(Preorder::class);
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'produk_id', 'id');
    }

    // Getter untuk mengambil merek dari produk
    public function getMerekAttribute()
    {
        return $this->produk->merek ?? '-';
    }

    // Getter untuk mengambil pabrikasi dari supplier (relasi ke Supplier via Produk)
    public function getPabrikasiAttribute()
    {
        return $this->produk->supplier->name ?? '-';
    }

    // Getter untuk mengambil kategori dari Produk
    public function getKategoriAttribute()
    {
        return $this->produk->kategori->name ?? '-';
    }

    // Getter untuk mengambil satuan dari Produk
    public function getSatuanAttribute()
    {
        return $this->produk->satuan->name ?? '-';
    }
    public function getHargaPajakAttribute()
    {
        return $this->harga * ($this->ppn / 100);
    }

    public function getHargaSetelahPajakAttribute()
    {
        return $this->harga + ($this->harga * ($this->ppn / 100));
    }

    public function getTotalHargaSetelahPajakAttribute()
    {
        return $this->harga_setelah_pajak * $this->qty;
    }
    // public function getHargaJualSatuanAttribute()
    // {
    //     return $this->total_harga_setelah_pajak / 0.7;
    // }
    public function getSelisihPaguAttribute()
    {
        return $this->pagu - $this->harga_jual_satuan;
    }
    public function getTotalJualAttribute()
    {
        return $this->harga_jual_satuan * $this->qty;
    }
}
