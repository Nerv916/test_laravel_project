<?php

namespace App\Http\Controllers;

use App\Models\Preorder;
use Illuminate\Http\Request;
use App\Models\StockMovement;
use App\Exports\LaporanExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Session;


class LaporanController extends Controller
{


    public function index(Request $request)
    {
        $stokIn = StockMovement::with('produk.barang')->where('type', 'in');
        $stokOut = StockMovement::with('produk.barang')->where('type', 'out');

        // Filter berdasarkan tanggal jika ada input dari form
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $stokIn->whereBetween('created_at', [$request->start_date, $request->end_date]);
            $stokOut->whereBetween('created_at', [$request->start_date, $request->end_date]);
        }

        return view('laporan.list', [
            'stokIn' => $stokIn->latest()->get(),
            'stokOut' => $stokOut->latest()->get(),
        ]);
    }
    public function export(Request $request)
    {
        return Excel::download(new LaporanExport($request->start_date, $request->end_date), 'laporan_transaksi.xlsx');
    }

    public function printSpb($id)
    {
        $preorder = Preorder::with('items')->findOrFail($id);

        $pdf = Pdf::loadView('laporan.spb', compact('preorder'))->setPaper('a4', 'portrait');

        return $pdf->stream("Surat-Pesanan-Barang-{$preorder->no_spo}.pdf");
    }
}
