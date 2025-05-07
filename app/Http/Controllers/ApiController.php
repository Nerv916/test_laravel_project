<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produk;
use App\Models\Kategori;
use App\Models\Satuan;
use App\Models\Supplier;

class ApiController extends Controller
{
    // Ambil daftar produk
    public function getProducts()
    {
        return response()->json(Produk::all());
    }

    // Ambil daftar kategori
    public function getKategori()
    {
        return response()->json(Kategori::all());
    }

    // Ambil daftar satuan
    public function getSatuan()
    {
        return response()->json(Satuan::all());
    }

    // Ambil daftar supplier
    public function getSuppliers()
    {
        return response()->json(Supplier::all());
    }
}