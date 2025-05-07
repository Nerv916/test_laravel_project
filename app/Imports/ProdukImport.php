<?php

namespace App\Imports;

use App\Models\Barang;
use App\Models\Produk;
use App\Models\Satuan;
use App\Models\Kategori;
use App\Models\Supplier;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProdukImport implements ToModel, WithHeadingRow, WithStartRow
{
    /**
     * Mulai membaca dari baris ke-2 untuk melewati header
     */
    public function startRow(): int
    {
        return 2;
    }
    public function model(array $row)
    {
        Log::info('Importing row: ', $row);

        try {
            $kategori = Kategori::firstOrCreate(['name' => $row['kategori']]);
            $supplier = Supplier::firstOrCreate(['name' => $row['pabrikasi']]);
            $satuan   = Satuan::firstOrCreate(['name' => $row['satuan']]);
            Log::info('Nama barang dari Excel: ' . $row['nama_barang']);
            $barang   = Barang::firstOrCreate(['nama' => $row['nama_barang']]);
            Log::info('Barang yang dibuat/diambil: ', $barang->toArray());


            $produkExists = Produk::where('barang_id', $barang->id)
                ->where('merek', $row['merek'])
                ->exists();

            if ($produkExists) {
                Log::info('Produk sudah ada: ' . $row['nama_barang'] . ' - ' . $row['merek']);
                return null;
            }

            return new Produk([
                'barang_id'    => $barang->id,
                'merek'        => $row['merek'],
                'kategori_id'  => $kategori->id,
                'supplier_id'  => $supplier->id,
                'satuan_id'    => $satuan->id,
                'nie'          => $row['nie'],
            ]);
        } catch (\Exception $e) {
            Log::error('Error importing row: ' . $e->getMessage());
            return null;
        }
    }
}
