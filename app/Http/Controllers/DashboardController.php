<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Tetap: Pembelian terbesar per supplier
        $pembelianTerbesar = DB::table('approved_items')
            ->join('produks', 'approved_items.produk_id', '=', 'produks.id')
            ->join('suppliers', 'produks.supplier_id', '=', 'suppliers.id')
            ->select('suppliers.name as supplier_name', DB::raw('SUM(approved_items.total_harga) as total_pembelian'))
            ->groupBy('suppliers.name')
            ->orderByDesc('total_pembelian')
            ->take(10)
            ->get();



        return view('dashboard', compact('pembelianTerbesar',));
    }
    public function penjualanPeriode(Request $request)
    {
        $filter = $request->query('filter', 'bulanan');

        // Format tanggal sesuai filter
        $format = match ($filter) {
            'harian' => '%Y-%m-%d',
            'mingguan' => '%x-%v', // ISO week
            default => '%Y-%m'
        };

        $penjualan = DB::table('stock_movements')
            ->where('type', 'out')
            ->select(
                DB::raw("DATE_FORMAT(created_at, '$format') as tanggal"),
                DB::raw('SUM(total_harga_jual) as total')
            )
            ->groupBy(DB::raw("DATE_FORMAT(created_at, '$format')"))
            ->orderBy('tanggal')
            ->get();

        return response()->json([
            'penjualan' => $penjualan,

        ]);
    }
    public function barangTerlaris(Request $request)
    {
        $limit = (int) $request->query('limit', 10);

        $barang = DB::table('stock_movements')
            ->join('produks', 'stock_movements.produk_id', '=', 'produks.id')
            ->join('barangs', 'produks.barang_id', '=', 'barangs.id')
            ->where('stock_movements.type', 'out')
            ->select('barangs.nama as nama_barang', DB::raw('SUM(quantity) as total_terjual'))
            ->groupBy('barangs.nama')
            ->orderByDesc('total_terjual')
            ->limit($limit)
            ->get();

        return response()->json(['barang' => $barang]);
    }
}
