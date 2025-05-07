<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Produk;
use App\Models\Satuan;
use App\Models\Kategori;
use App\Models\Supplier;
use Illuminate\Http\Request;
use App\Models\ApprovedItems;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ApiProdukController extends Controller
{
    public function getSatuanList()
    {
        return response()->json(Satuan::all());
    }
    public function getSupplierList()
    {
        return response()->json(Supplier::all());
    }
    public function getKategoriList()
    {
        return response()->json(Kategori::all());
    }
    public function getNamaBarangList()
    {
        return response()->json(Barang::all());
    }

    public function getProdukList(): JsonResponse
    {
        $produk = Produk::with(['barang', 'satuan', 'kategori', 'supplier', 'stockMovements'])->get()->map(function ($item) {
            $stokMasuk = $item->stockMovements->where('type', 'in')->sum('quantity');
            $stokKeluar = $item->stockMovements->where('type', 'out')->sum('quantity');
            $stokTersedia = $stokMasuk - $stokKeluar;

            // Ambil harga terbaru dari ApprovedItems
            $latestHarga = ApprovedItems::where('produk_id', $item->id)
                ->orderByDesc('created_at')
                ->value('harga');

            return [
                'id' => $item->id,
                'nama_barang' => $item->barang->nama ?? '',
                'merek' => $item->merek ?? '',
                'kategori' => $item->kategori,
                'satuan' => $item->satuan,
                'supplier' => $item->supplier,
                'supplier_name' => $item->supplier->name ?? '-',
                'nie' => $item->nie,
                'stok_tersedia' => $stokTersedia,
                'stokin_harga' => $latestHarga, // ← Tambahkan ini
                'supplier_ppn' => $item->supplier->pajak ?? 11, // ← ini penting
            ];
        });

        return response()->json($produk);
    }
    public function getLatestHarga($produk_id)
    {
        $latest = ApprovedItems::where('produk_id', $produk_id)
            ->orderByDesc('created_at')
            ->first();

        return response()->json([
            'harga' => $latest?->harga ?? null
        ]);
    }
    public function getProdukByName($nama): JsonResponse
    {
        $produk = Produk::with(['barang', 'satuan', 'kategori'])
            ->whereHas('barang', function ($q) use ($nama) {
                $q->where('nama', 'LIKE', '%' . $nama . '%');
            })
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'nama_barang' => $item->barang->nama ?? '',
                    'merek' => $item->merek ?? '',
                    'kategori' => $item->kategori,
                    'satuan' => $item->satuan,
                ];
            });

        return response()->json([
            'success' => $produk->isNotEmpty(),
            'message' => $produk->isNotEmpty() ? 'Produk ditemukan' : 'Produk tidak ditemukan',
            'data' => $produk
        ]);
    }
    public function getMerekOptions($nama_barang)
    {
        $mereks = Produk::whereHas('barang', function ($q) use ($nama_barang) {
            $q->whereRaw('LOWER(nama) = ?', [strtolower(trim($nama_barang))]);
        })
            ->distinct()
            ->pluck('merek');

        return response()->json($mereks);
    }

    public function getProdukListByNamaBarang($nama_barang)
    {
        Log::info('Nama Barang dari Request:', ['nama_barang' => $nama_barang]);

        $produkList = Produk::whereHas('barang', function ($q) use ($nama_barang) {
            $q->whereRaw('LOWER(nama) = ?', [strtolower(trim($nama_barang))]);
        })
            ->with(['kategori', 'satuan'])
            ->get();

        Log::info('Jumlah Produk Ditemukan:', ['count' => $produkList->count()]);

        if ($produkList->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Produk tidak ditemukan!',
                'data' => []
            ], 404);
        }

        return response()->json([
            'success' => true,
            'count' => $produkList->count(),
            'data' => $produkList
        ]);
    }
}
