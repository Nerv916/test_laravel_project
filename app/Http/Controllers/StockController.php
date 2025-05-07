<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\Approved;
use App\Models\Preorder;
use App\Models\ItemOrder;
use Illuminate\Http\Request;
use App\Models\ApprovedItems;
use App\Models\StockMovement;
use App\Services\StockService;
use Illuminate\Support\Facades\Log;

class StockController extends Controller
{
    protected $stockService;

    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
    }
    public function stockIn()
    {

        $produks = Produk::with('supplier')->get()->map(function ($produk) {
            $stokMasuk = StockMovement::where('produk_id', $produk->id)
                ->where('type', 'in')
                ->sum('quantity');
            $stokKeluar = StockMovement::where('produk_id', $produk->id)
                ->where('type', 'out')
                ->sum('quantity');

            $produk->stok = $stokMasuk - $stokKeluar;
            return $produk;
        });

        return view('stok.stokin', compact('produks'));
    }
    public function stokOut()
    {
        $produks = Produk::with('supplier')->get()->map(function ($produk) {
            $stokMasuk = StockMovement::where('produk_id', $produk->id)
                ->where('type', 'in')
                ->sum('quantity');
            $stokKeluar = StockMovement::where('produk_id', $produk->id)
                ->where('type', 'out')
                ->sum('quantity');

            $produk->stok = $stokMasuk - $stokKeluar;
            return $produk;
        });
        return view('stok.stokout', compact('produks'));
    }
    public function inputStock(Request $request)
    {
        $approvedItems = ApprovedItems::where('approved_id', $request->approved_id)->get();
        $approved = Approved::with('preorder')->findOrFail($request->approved_id);
        $parentId = $approved->parent_id ?? $approved->preorder->parent_id ?? null;

        foreach ($approvedItems as $item) {
            $id = $item->id;
            $qty = $request->qty[$id] ?? 0;

            // Skip kalau qty kosong atau nol
            if (!$qty || $qty <= 0) continue;

            $noBatch        = $request->no_batch[$id] ?? '-';
            $expDate        = $request->exp_date[$id] ?? null;
            $hargaBeliRaw   = $request->harga_beli[$id] ?? '0';
            $ppn            = $request->ppn[$id] ?? 0;
            $hargaJualTotal = $request->harga_jual[$id] ?? 0;

            // Bersihkan harga beli dari format Rp dan titik/koma
            $hargaBeli = preg_replace('/[^\d]/', '', $hargaBeliRaw);
            $hargaBeli = intval($hargaBeli);

            // Menghitung total harga beli
            $totalHargaBeli = $hargaBeli * $qty;

            StockMovement::create([
                'preorder_id'       => $item->approved->preorder_id,
                'produk_id'         => $item->produk_id,
                'approved_id'       => $item->approved_id,
                'approved_item_id'  => $id,
                'parent_id'         => $parentId ?? null,
                'type'              => 'in',
                'quantity'          => $qty,
                'harga_beli'        => $hargaBeli,
                'ppn'               => $ppn,
                'no_batch'          => $noBatch,
                'exp_date'          => $expDate,
                'description' => 'Stok masuk dari order #' . $item->preorder_id,
                'total_harga_jual'  => $hargaJualTotal,
                'total_harga_beli'  => $totalHargaBeli,  // Menyimpan total harga beli
            ]);
        }



        return back()->with('success', 'Stok berhasil ditambahkan.');
    }





    public function stockOutStore(Request $request)
    {
        // $approvedItems = ApprovedItems::where('approved_id', $request->approved_id)->get();
        $approved = Approved::with('preorder')->findOrFail($request->approved_id);
        $parentId = $approved->parent_id ?? $approved->preorder->parent_id ?? null;


        Log::info("StockOut function called", ['data' => $request->all()]);

        $validated = $request->validate([
            'produk_id' => 'required|array',
            'batch' => 'required|array',
            'exp_date' => 'required|array',
            'qty' => 'required|array',
            'harga_beli' => 'required|array',
            'ppn' => 'nullable|array',
        ]);

        foreach ($validated['produk_id'] as $key => $produkId) {
            $produk = Produk::find($produkId);
            if (!$produk) {
                Log::error("Produk tidak ditemukan!", ['produk_id' => $produkId]);
                continue;
            }

            $qty = (int) $validated['qty'][$key];
            $batch = $validated['batch'][$key];
            $expDate = $validated['exp_date'][$key];
            $hargaBeli = (int) str_replace('.', '', $validated['harga_beli'][$key]); // handle format ribuan
            $ppn = isset($validated['ppn'][$key]) ? (float) $validated['ppn'][$key] : 0;

            if ($qty > 0) {
                $ppnValue = ($hargaBeli * $ppn / 100);
                $totalHargaJual = ($hargaBeli + $ppnValue) * $qty;

                try {
                    StockMovement::create([
                        'produk_id'         => $produkId,
                        'type'              => 'out',
                        'quantity'          => $qty,
                        'harga_beli'        => $hargaBeli,
                        'ppn'               => $ppn,
                        'total_harga_jual'  => $totalHargaJual,
                        'no_batch'          => $batch,
                        'exp_date'          => $expDate,
                        'description'       => "Penjualan - Batch: $batch, Exp: $expDate",
                        'parent_id'         => $parentId,
                    ]);
                } catch (\Exception $e) {
                    Log::error('Error inserting stock movement', ['error' => $e->getMessage()]);
                    return back()->with('error', 'Gagal mengurangi stok.');
                }
            }
        }

        return back()->with('success', 'Stok berhasil dikurangi.');
    }
    public function searchBatch(Request $request)
    {
        $produkId = $request->query('produk_id');
        $query = $request->query('query');

        $batches = StockMovement::select('no_batch', 'exp_date', 'harga_beli', 'produk_id', 'ppn') // ⬅️ tambahkan 'ppn'
            ->where('produk_id', $produkId)
            ->where('type', 'in')
            ->where('no_batch', 'like', '%' . $query . '%')
            ->groupBy('no_batch', 'exp_date', 'harga_beli', 'produk_id', 'ppn') // ⬅️ tambahkan 'ppn'
            ->get()
            ->map(function ($item) {
                $stok = StockMovement::where('produk_id', $item->produk_id)
                    ->where('no_batch', $item->no_batch)
                    ->where('type', 'in')->sum('quantity')
                    - StockMovement::where('produk_id', $item->produk_id)
                    ->where('no_batch', $item->no_batch)
                    ->where('type', 'out')->sum('quantity');

                return [
                    'no_batch' => $item->no_batch,
                    'exp_date' => $item->exp_date,
                    'harga_beli' => number_format($item->harga_beli, 0, ',', '.'),
                    'ppn' => $item->ppn, // ⬅️ tambahkan ini
                    'stok' => $stok,
                ];
            });


        return response()->json($batches);
    }
}
