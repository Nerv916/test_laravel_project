<?php

namespace App\Exports;

use App\Models\StockMovement;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Collection;

class StockMovementExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return StockMovement::selectRaw('
            barangs.nama as nama_barang,
            produks.merek,
            SUM(CASE WHEN stock_movements.type = "in" THEN quantity ELSE 0 END) as total_in,
            SUM(CASE WHEN stock_movements.type = "out" THEN quantity ELSE 0 END) as total_out,
            (SUM(CASE WHEN stock_movements.type = "in" THEN quantity ELSE 0 END) - SUM(CASE WHEN stock_movements.type = "out" THEN quantity ELSE 0 END)) as stok_sisa,
            MAX(stock_movements.harga_beli) as harga_beli,
            MAX(stock_movements.ppn) as ppn
        ')
            ->join('produks', 'stock_movements.produk_id', '=', 'produks.id')
            ->join('barangs', 'produks.barang_id', '=', 'barangs.id')
            ->groupBy('produks.id', 'barangs.nama', 'produks.merek')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Nama Barang',
            'Merek',
            'Total Masuk',
            'Total Keluar',
            'Stok Sisa',
            'Harga Beli',
            'PPN'
        ];
    }
}
