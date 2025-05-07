<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\Approved;
use App\Models\Costumer;
use App\Models\Preorder;
use Illuminate\Http\Request;
use App\Models\ApprovedItems;
use App\Models\StockMovement;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use App\Helpers\ConvertRupiah;



class PdfController extends Controller
{
    public function previewPenawaran(Request $request)
    {
        $request->validate([
            'items' => 'required|json',
            'preorder_id' => 'required|exists:preorders,id',
        ]);

        $items = collect(json_decode($request->input('items')))
            ->filter(fn($item) => $item->status === 'approved');




        $preorder = Preorder::with('customer')->findOrFail($request->preorder_id);

        // Optional: enrich items if needed (e.g., ambil produk info lengkap)
        $produkIds = $items->pluck('produk_id')->unique();
        $produkMap = Produk::with('barang', 'kategori')->whereIn('id', $produkIds)->get()->keyBy('id');




        $enrichedItems = $items->map(function ($item) use ($produkMap) {
            $produk = $produkMap[$item->produk_id] ?? null;

            $margin = (float) ($item->margin ?? 1);
            $hargaSetelahPajak = (float) ($item->harga_setelah_pajak ?? 0);
            $qty = (float) ($item->qty ?? 0);

            $hargaJualSatuan = $item->harga_jual_satuan ?? ($hargaSetelahPajak / $margin);
            $totalHarga = $item->total_harga_jual ?? ($hargaJualSatuan * $qty);

            return (object)[
                'produk_id' => $item->produk_id,
                'produk' => $produk,
                'qty' => $qty,
                'margin' => $margin,
                'harga' => $item->harga,
                'ppn' => $item->ppn ?? 0,
                'harga_setelah_pajak' => $hargaSetelahPajak,
                'harga_jual_satuan' => $hargaJualSatuan,
                'total_harga' => $totalHarga,
            ];
        });

        $pdf = Pdf::loadView('pdf.penawaran_barang', [
            'preorder' => $preorder,
            'items' => $enrichedItems,
        ])->setPaper('A4', 'portrait');

        return $pdf->stream('penawaran-barang-preview.pdf');
    }

    public function generatePDF($id)
    {
        // Ambil data Approved dengan items dan produk (berdasarkan supplier)
        $approved = Approved::with('items.produk.supplier')->findOrFail($id);

        // Kelompokkan berdasarkan supplier
        $supplierOrders = $approved->items->groupBy(fn($item) => $item->produk->supplier->id ?? 'no_supplier');

        // Ambil user
        $user = Auth::user();

        // Inisialisasi array untuk menyimpan PDF
        $pdfFiles = [];

        // Generate PDF untuk setiap supplier
        foreach ($supplierOrders as $supplierId => $items) {
            // Ambil nama supplier (gunakan "Tanpa Supplier" jika null)
            $supplierName = optional($items->first()->produk->supplier)->name ?? 'Tanpa-Supplier';

            // Format nama file PDF
            $formattedSupplierName = str_replace([' ', '/'], '-', $supplierName); // Menghindari karakter tidak valid
            $fileName = "Surat-Pesanan-{$formattedSupplierName}-{$approved->id}.pdf";

            // Generate PDF hanya untuk supplier ini
            $pdf = PDF::loadView('pdf.surat_pesanan', [
                'approved' => $approved,
                'supplierOrders' => [$supplierId => $items], // Hanya untuk supplier ini
                'user' => $user
            ])->setPaper('A4', 'portrait');

            // Simpan PDF ke array
            $pdfFiles[] = [
                'filename' => $fileName,
                'pdf' => $pdf
            ];
        }

        // Jika hanya satu supplier, langsung tampilkan PDF
        if (count($pdfFiles) === 1) {
            return $pdfFiles[0]['pdf']->stream($pdfFiles[0]['filename']);
        }

        // Jika lebih dari satu supplier, buat file ZIP
        $zipFileName = "Surat-Pesanan-{$approved->id}.zip";
        $zipPath = storage_path('app/' . $zipFileName);

        $zip = new \ZipArchive;
        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === TRUE) {
            foreach ($pdfFiles as $file) {
                $pdfContent = $file['pdf']->output();
                $zip->addFromString($file['filename'], $pdfContent);
            }
            $zip->close();
        }

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }


    public function penerimaanPDF($id)
    {
        $approved = Approved::with([
            'items.produk.costumer',
            'items.produk.supplier'
        ])->findOrFail($id);

        $user = Auth::user();
        $costumer = Costumer::where('name', $approved->name_cust)->first();

        $stockMovements = StockMovement::whereIn('produk_id', $approved->items->pluck('produk_id'))
            ->where('type', 'out')
            ->get()
            ->groupBy('produk_id');


        $pdf = PDF::loadView('pdf.tanda_terima', compact('approved', 'costumer', 'user', 'stockMovements'))
            ->setPaper('A4', 'portrait');

        return $pdf->stream("penerimaan_barang_$id.pdf");
    }
    public function printInvoice($id)
    {
        $approved = Approved::with(['items.produk' => function ($query) {
            $query->with(['stockMovements' => function ($query) {
                $query->where('type', 'out'); // Hanya ambil stok keluar
            }]);
        }])->findOrFail($id);
        $costumer = Costumer::where('name', $approved->name_cust)->first();
        $noSPO = $approved->no_spo;


        $total = 0;
        $totalPPN = 0;
        $total_tagihan = 0;
        $ppn = 0;
        


        foreach ($approved->items as $item) {

            $stockout = $item->produk->stockMovements->sum('quantity');

            $harga_jual = $item->harga_jual_satuan ?? 0;
            $subtotal = $stockout * $harga_jual;
            $harga = $item->harga;
            $ppn = $item->ppn ?? 0.11;
            $total_tagihan += $subtotal;
            $ppntotal = $total_tagihan / $ppn;
            $dpp = $total_tagihan - $ppntotal;
            $totalsemua = $ppntotal + $dpp;





            $item->ppntotal = $ppntotal;
            $item->dpp = $dpp;
            $item->stockout = $stockout;
            $item->subtotal = $subtotal;
            $item->subtotal_terbilang = terbilang($item->totalsemua) . ' rupiah';
            $item->total_tagihan = $total_tagihan;
            $item->totalsemua = $totalsemua;

            $total += $subtotal;
            // dd($item);
        }

        $pdf = Pdf::loadView('pdf.invoice', compact('approved', 'total', 'costumer', 'noSPO'))
            ->setPaper('A4', 'portrait');

        return $pdf->stream('invoice.pdf');
    }
}
