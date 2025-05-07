<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\Approved;
use App\Models\Costumer;
use App\Models\Preorder;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\ApprovedItems;
use App\Models\StockMovement;
use Illuminate\Support\Facades\Log;

class OrderLanjutanController extends Controller
{
    public function createOrderLanjutan(Request $request)
    {
        try {
            // Ambil Approved berdasarkan ID yang dikirim (sebenarnya ini parent_id dari sisi frontend)
            $approved = Approved::findOrFail($request->parent_id);

            // Ambil preorder yang terkait sama approved ini
            $parent = $approved->preorder; // ini preorder "lama", bukan order lanjutan
            $customer = $parent?->customer;

            if (!$parent) {
                throw new \Exception('Parent order tidak ditemukan dari approved.');
            }

            $no_spo = 'SPO-' . now()->format('Ymd-His') . '-' . Str::upper(Str::random(4));

            Log::info('Buat order lanjutan sukses:', [
                'produk_id' => $request->produk_id,
                'approved_id' => $approved->id,
                'preorder_id' => $parent->id,
                'no_spo' => $no_spo
            ]);

            return response()->json([
                'redirect_url' => route('preorder.lanjutan', [
                    'id' => $parent->id, // preorder ID
                    'no_spo' => $no_spo,
                    'cust_id' => $customer->id ?? '',
                    'no_surat' => $request->no_surat
                ])
            ]);
        } catch (\Exception $e) {
            Log::error('Gagal buat order lanjutan', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Terjadi kesalahan di server.'
            ], 500);
        }
    }





    public function lanjutan($id, Request $request)
    {
        $order = Preorder::findOrFail($id);

        // Ambil approved berdasarkan no_surat
        $approved = Approved::where('no_surat', $request->no_surat)->first();

        $itemsWithStock = collect();

        if ($approved) {
            $approvedItems = $approved->items()->with('produk.barang')->get();

            $stockMovements = StockMovement::with('produk.barang')
                ->whereIn('approved_item_id', $approvedItems->pluck('id'))
                ->get();

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
            })->filter(fn($item) => $item->qty_kurang > 0);
        }

        return view('pesanan.create', [
            'id' => $id,
            'no_spo' => $request->no_spo,
            'cust_id' => $request->cust_id,
            'no_surat' => $request->no_surat,
            'customers' => Costumer::all(),
            'order_date' => $order->order_date ?? now()->format('Y-m-d'),
            'is_lanjutan' => true,
            'catatanKurang' => $itemsWithStock, // Kirim hasilnya ke blade
        ]);
    }


    public function batalOrderLanjutan(Request $request)
    {
        return redirect()->route('stok.report.detail', ['approved' => $request->parent_id])
            ->with('info', 'Order lanjutan dibatalkan (belum disimpan).');
    }
    public function store(Request $request)
    {
        $request->validate([
            'produk_id' => 'required|exists:produks,id',
            'no_surat' => 'required|string|exists:preorders,no_surat',
        ]);

        $parent = Preorder::where('no_surat', $request->no_surat)->first();

        if (!$parent) {
            return response()->json(['success' => false, 'error' => 'Preorder tidak ditemukan.']);
        }

        // Cek jika sudah ada anak preorder
        $jumlahAnak = Preorder::where('parent_id', $parent->id)->count();
        $no_spo_baru = $parent->no_spo . '-' . ($jumlahAnak + 1);

        $newPreorder = Preorder::create([
            'parent_id'   => $parent->id,
            'no_spo'      => $no_spo_baru,
            'name_cust'   => $parent->name_cust,
            'no_surat'    => $parent->no_surat,
            'order_date'  => now(),
            'status'      => 'pending order',
        ]);

        return response()->json(['success' => true, 'preorder_id' => $newPreorder->id]);
    }
}
