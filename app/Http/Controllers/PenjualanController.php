<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\Approved;
use App\Models\Costumer;
use Illuminate\Http\Request;
use App\Models\ApprovedItems;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class PenjualanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $stockMovements = StockMovement::with('preorder', 'produk', 'approved')
            ->where('type', 'in')
            ->latest()
            ->get()
            ->groupBy('approved_id');

        $orders = $stockMovements->map(function ($group) {
            $first = $group->first();
            return (object)[
                'approved_id' => $first->approved_id,
                'order_date' => $first->approved->order_date ?? null,
                'no_surat' => $first->approved->no_surat ?? null,
                'name_cust' => $first->approved->name_cust ?? null,
                'parent_id' => $first->approved->parent_id ?? null,
                'total_harga_jual' => $group->sum('total_harga_jual'),
            ];
        });

        return view('penjualan.list', compact('orders'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Ambil semua stock movement yang berhubungan dengan approved_id tertentu
        $stockMovements = StockMovement::with('produk.barang', 'approved', 'approvedItem', 'preorder')
            ->where('approved_id', $id)
            ->where('type', 'in')
            ->get();
        // dd($stockMovements);

        $user = Auth::user();
        $approvedId = $stockMovements->first()->approved_id ?? null;
        $approvedOrders = $stockMovements->first()->approved ?? null;

        return view('penjualan.show', compact('stockMovements', 'user', 'approvedId', 'approvedOrders'));
    }
    public function store(Request $request)
    {

        Log::info('🔥 Masuk store PenjualanController');
        Log::info('URL yang dipanggil: ' . $request->url());
        Log::info('Method yang diterima: ' . request()->method());
        Log::info('Sampai akhir store(), mau redirect ke /penjualan');
        $approvedItems = ApprovedItems::where('approved_id', $request->approved_id)->get();
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
                    Log::info('Menyimpan Stok Keluar', [
                        'produk_id' => $produkId,
                        'qty' => $qty,
                        'batch' => $batch,
                        'exp_date' => $expDate,
                        'harga_beli' => $hargaBeli,
                        'ppn' => $ppn,
                    ]);
                } catch (\Exception $e) {
                    Log::error('Error inserting stock movement', ['error' => $e->getMessage()]);
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Redirect berhasil, route /penjualan harusnya ada',
                    ]);
                }
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Redirect berhasil, route /penjualan harusnya ada',
        ]);
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
