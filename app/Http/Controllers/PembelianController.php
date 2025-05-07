<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Approved;
use Illuminate\Http\Request;
use App\Models\ApprovedItems;

class PembelianController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $approveds = Approved::with('items.produk')->get();

        // Menjumlahkan total harga dari semua approved items
        $totalSemua = ApprovedItems::sum('total_harga');

        return view('pembelian.list', compact('approveds', 'totalSemua'));
    }


    public function show(string $id)
    {
        $approved = Approved::with([
            'preorder.customer',
            'items.produk.supplier.barang'
        ])->findOrFail($id);

        // Kelompokkan item berdasarkan supplier_id (jika null, pakai 'no_supplier')
        $supplierOrders = $approved->items->groupBy(function ($item) {
            return optional($item->produk->supplier)->id ?? 'no_supplier';
        });

        $barang = Barang::all();

        // Ambil supplier pertama yang ditemukan (buat input hidden)
        $firstSupplier = $approved->items->firstWhere('produk.supplier_id', '!=', null)?->produk->supplier->id;

        return view('pembelian.show', compact('approved', 'supplierOrders', 'firstSupplier', 'barang'));
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
