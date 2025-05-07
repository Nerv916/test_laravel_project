<?php

namespace App\Http\Controllers;

use App\Models\Approved;
use Illuminate\Http\Request;
use App\Models\ApprovedItems;
use App\Models\StockMovement;
use Illuminate\Support\Facades\Log;

class StockOutController extends Controller
{
    public function store(Request $request)
    {
        $approvedItems = ApprovedItems::where('approved_id', $request->approved_id)->get();
        $approved = Approved::with('preorder')->findOrFail($request->approved_id);
        $parentId = $approved->parent_id ?? $approved->preorder->parent_id ?? null;
    
        foreach ($approvedItems as $item) {
            $id = $item->id;
            $qty = $request->qty[$id] ?? 0;
    
            // Skip kalau qty kosong atau nol
            if (!$qty || $qty <= 0) continue;
    
            $noBatch        = $request->batch[$id] ?? '-';
            $expDate        = $request->exp_date[$id] ?? null;
            $hargaBeliRaw   = $request->harga_beli[$id] ?? '0';
            $ppn            = $request->ppn[$id] ?? 0;
            $hargaJualTotal = $request->total_harga_jual[$id] ?? 0;
    
            // Bersihkan harga beli dari format Rp dan titik/koma
            $hargaBeli = preg_replace('/[^\d]/', '', $hargaBeliRaw);
            $hargaBeli = intval($hargaBeli);
    
            // Menghitung total harga beli
            $totalHargaBeli = $hargaBeli * $qty;
    
            try {
                StockMovement::create([
                    'preorder_id'       => $item->approved->preorder_id,
                    'produk_id'         => $item->produk_id,
                    'approved_id'       => $item->approved_id,
                    'approved_item_id'  => $id,
                    'parent_id'         => $parentId ?? null,
                    'type'              => 'out',
                    'quantity'          => $qty,
                    'harga_beli'        => $hargaBeli,
                    'ppn'               => $ppn,
                    'no_batch'          => $noBatch,
                    'exp_date'          => $expDate,
                    'description'       => 'Penjualan - Batch: ' . $noBatch . ', Exp: ' . $expDate,
                    'total_harga_jual'  => $hargaJualTotal,
                    'total_harga_beli'  => $totalHargaBeli,
                ]);
            } catch (\Exception $e) {
                Log::error('Gagal simpan stok keluar', ['error' => $e->getMessage()]);
                return redirect()->back()->with('error', 'Gagal simpan stok keluar');
            }
        }
    
        return redirect()->route('penjualan.index')->with('success', 'Stok keluar berhasil disimpan');
    }
    
    
}
