<?php

namespace App\Http\Controllers;

use App\Models\Approved;
use Illuminate\Http\Request;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use App\Exports\StockMovementExport;
use Maatwebsite\Excel\Facades\Excel;

class ReportStockController extends Controller
{
    // public function index()
    // {
    //     $stockData = StockMovement::with(['produk.barang'])
    //         ->orderBy('created_at', 'desc')
    //         ->get()
    //         ->map(function ($item) {
    //             return [
    //                 'id' => $item->id,
    //                 'nama_barang' => $item->produk->barang->name ?? '-',
    //                 'merek' => $item->produk->merek ?? '-',
    //                 'qty' => $item->quantity,
    //                 'type' => $item->type,
    //                 'harga_beli' => $item->harga_beli,
    //                 'no_batch' => $item->no_batch,
    //                 'exp_date' => $item->exp_date,
    //                 'total_harga_jual' => $item->total_harga_jual,
    //                 'description' => $item->description,
    //                 'tanggal' => $item->created_at->format('d-m-Y'),
    //             ];
    //         });

    //     return view(view: 'laporan.list', compact('stockData'));
    // }
    public function reportIndex()
    {
        $approvedOrders = Approved::with('customer') // pastikan relasi customer ada
            ->whereHas('stockMovements')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('laporan.stockIn.index', compact('approvedOrders'));
    }
    public function reportDetail(Approved $approved)
    {
        // Ambil semua item dari approved termasuk produk dan barangnya
        $approvedItems = $approved->items()->with('produk.barang')->get();

        // Ambil semua stock movement berdasarkan approved_item_id
        $stockMovements = StockMovement::with('produk.barang')
            ->whereIn('approved_item_id', $approvedItems->pluck('id'))
            ->get();

        // Gabungkan data approved_item dan stock yang sudah masuk
        $itemsWithStock = $approvedItems->map(function ($item) use ($stockMovements) {
            $relatedStocks = $stockMovements->where('approved_item_id', $item->id);
            $totalQtyMasuk = $relatedStocks->sum('quantity');
            $sisaQty = max($item->qty - $totalQtyMasuk, 0);

            return (object) [
                'approved_item' => $item,
                'produk' => $item->produk,
                'barang' => $item->produk->barang ?? null,
                'qty_approved' => $item->qty,
                'qty_masuk' => $totalQtyMasuk,
                'qty_kurang' => $sisaQty,
                'stock_movements' => $relatedStocks,
            ];
        });

        return view('laporan.stockIn.detail', compact('approved', 'itemsWithStock'));
    }

    public function StockReport(Request $request)
    {
        $keyword = $request->keyword;

        // Subquery: ambil created_at terakhir per produk
        $maxDateSubquery = DB::table('stock_movements')
            ->select('produk_id', DB::raw('MAX(created_at) as max_created_at'))
            ->groupBy('produk_id');

        // Subquery untuk harga terbaru per produk
        $latestHarga = DB::table('stock_movements as sm')
            ->joinSub($maxDateSubquery, 'latest', function ($join) {
                $join->on('sm.produk_id', '=', 'latest.produk_id')
                    ->on('sm.created_at', '=', 'latest.max_created_at');
            })
            ->select('sm.produk_id', 'sm.harga_beli', 'sm.ppn');

            $stockData = StockMovement::selectRaw('
            produks.id,
            barangs.nama as nama_barang,
            produks.merek,
            SUM(CASE WHEN stock_movements.type = "in" THEN quantity ELSE 0 END) as total_in,
            SUM(CASE WHEN stock_movements.type = "out" THEN quantity ELSE 0 END) as total_out,
            MAX(stock_movements.created_at) as tanggal,
            latest_harga.harga_beli,
            latest_harga.ppn,
            MAX(CASE WHEN stock_movements.parent_id IS NOT NULL THEN 1 ELSE 0 END) as is_lanjutan,
            (SELECT SUM(qty) FROM approved_items WHERE approved_items.produk_id = produks.id) as approved_qty
        ')
        
            ->join('produks', 'stock_movements.produk_id', '=', 'produks.id')
            ->join('barangs', 'produks.barang_id', '=', 'barangs.id')
            ->joinSub($latestHarga, 'latest_harga', function ($join) {
                $join->on('stock_movements.produk_id', '=', 'latest_harga.produk_id');
            })
            ->when($keyword, function ($query, $keyword) {
                $query->where('barangs.nama', 'like', '%' . $keyword . '%');
            })
            ->groupBy(
                'produks.id',
                'barangs.nama',
                'produks.merek',
                'latest_harga.harga_beli',
                'latest_harga.ppn'
            )
            ->get()
            ->map(function ($item) {
                $item->stok_sisa = $item->total_in - $item->total_out;
                $item->is_lanjutan = (bool) $item->is_lanjutan;
                return $item;
            });

        return view('laporan.report_stock', compact('stockData'));
    }

    public function ExStockReport(Request $request)
    {
        $keyword = $request->keyword;

        $stockData = StockMovement::selectRaw('
            produks.id,
            barangs.nama as nama_barang,
            produks.merek,
            SUM(CASE WHEN stock_movements.type = "in" THEN quantity ELSE 0 END) as total_in,
            SUM(CASE WHEN stock_movements.type = "out" THEN quantity ELSE 0 END) as total_out,
            MAX(stock_movements.harga_beli) as harga_beli,
            MAX(stock_movements.ppn) as ppn,
            MAX(stock_movements.created_at) as tanggal
        ')
            ->join('produks', 'stock_movements.produk_id', '=', 'produks.id')
            ->join('barangs', 'produks.barang_id', '=', 'barangs.id')
            ->when($keyword, function ($query, $keyword) {
                $query->where('barangs.nama', 'like', '%' . $keyword . '%');
            })
            ->groupBy('produks.id', 'barangs.nama', 'produks.merek')
            ->get()
            ->map(function ($item) {
                $item->stok_sisa = $item->total_in - $item->total_out;
                return $item;
            });

        return view('laporan.list', compact('stockData'));
    }

    public function exportStockReport()
    {
        return Excel::download(new StockMovementExport, 'laporan_stok.xlsx');
    }

    public function laporanStockOut()
{
    $stockOuts = StockMovement::where('type', 'out')
        ->with(['produk', 'approved'])
        ->get()
        ->groupBy('approved_id'); // Group berdasarkan approved_id

    // Buat total per approved_id
    $totals = $stockOuts->map(function ($group) {
        return [
            'total_harga_beli' => $group->sum('total_harga_beli'),
            'total_harga_jual' => $group->sum('total_harga_jual'),
            'approved' => $group->first()->approved ?? null,
        ];
    });

    return view('laporan.stockout.list', compact('stockOuts', 'totals'));
}

}
