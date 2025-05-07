<?php

namespace App\Http\Controllers;

use App\Models\Approved;
use App\Models\Preorder;
use App\Models\ItemOrder;
use Illuminate\Http\Request;
use App\Models\ApprovedItems;
use App\Models\LaporanPreorder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Spatie\Permission\Middleware\PermissionMiddleware;

class LaporanPreorderController extends Controller implements HasMiddleware
{
    public static function middleware():array{
        return [
            new Middleware('permission:approved order', only: ['index']),
        ];


    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $preorder = Preorder::with('items.produk')->get();
        return view('polaporan.list', compact('preorder'));
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $preorder = Preorder::with(['customer', 'items.produk'])->findOrFail($id);
        // dd($preorder->items);
        if (!$preorder) {
            abort(404, 'Data tidak ditemukan'); // Menghindari error jika data tidak ditemukan
        }
        $items = ItemOrder::all();

        return view('polaporan.detail', compact('preorder'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,rejected'
        ]);

        $itemOrder = ItemOrder::findOrFail($id);
        $itemOrder->status = $request->status;
        $itemOrder->save();

        return response()->json(['success' => true, 'message' => 'Status berhasil diperbarui.']);
    }

    public function updateMargin(Request $request, $id)
    {
        try {
            Log::info('🔍 Incoming updateMargin', [
                'id' => $id,
                'body' => $request->all()
            ]);

            $validated = $request->validate([
                'margin' => 'required|numeric|min:0.01'
            ]);

            $item = ItemOrder::findOrFail($id);
            $item->margin = $validated['margin'];

            if ($item->harga_setelah_pajak && $validated['margin'] > 0) {
               
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Harga setelah pajak kosong atau margin tidak valid.',
                ], 422);
            }

            $item->save();

            return response()->json([
                'success' => true,
                'message' => 'Margin berhasil diperbarui.',
                'data' => [
                    'harga_jual_satuan' => $item->harga_jual_satuan
                ]
            ]);
        } catch (\Illuminate\Validation\ValidationException $ve) {
            // Tangani validasi biar gak dilempar sebagai 500
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $ve->errors()
            ], 422);
        } catch (\Throwable $e) {
            Log::error('🔥 updateMargin error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Server Error',
                'error' => $e->getMessage(), // biar bisa kelihatan error-nya di console
            ], 500);
        }
    }


    public function getPreorderItems($id)
    {
        $po = Preorder::with('items')->findOrFail($id);
        return response()->json(['items' => $po->items]);
    }

    public function approvePreorder(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            Log::info("Approve Preorder - ID: {$id}");

            $preorder = Preorder::with(['items' => function ($query) {
                $query->where('status', 'approved');
            }])->findOrFail($id);

            Log::info("Preorder ditemukan", ['preorder' => $preorder]);

            if ($preorder->items->isEmpty()) {
                Log::warning("Preorder ID {$id} tidak memiliki item yang disetujui");
                return response()->json(['error' => 'Tidak ada item yang disetujui'], 422);
            }

            $approved = Approved::create([
                'preorder_id' => $preorder->id,
                'parent_id' => $preorder->parent_id,
                'no_spo' => $preorder->no_spo ?? 'Tidak Ada',
                'name_cust' => optional($preorder->customer)->name ?? 'Tidak Diketahui',
                'no_surat' => $preorder->no_surat,
                'order_date' => $preorder->order_date,
            ]);

            Log::info("Approved created", ['approved_id' => $approved->id]);



            foreach ($request->items as $itemData) {
                Log::info('Data item dikirim:', $request->items);

                ApprovedItems::create([
                    'approved_id' => $approved->id,
                    'preorder_id' => $preorder->id,
                    'produk_id' => $itemData['produk_id'],
                    'qty' => $itemData['qty'],
                    'pagu' => $itemData['pagu'],
                    'satuan' => $itemData['satuan'], // kalau lo simpan satuan juga
                    'merek' => $itemData['merek'],   // kalau lo simpan merek juga
                    'ppn' => $itemData['ppn'],
                    'harga' => $itemData['harga'],
                    'harga_setelah_pajak' => $itemData['harga_setelah_pajak'],
                    'total_harga_beli' => $itemData['total_harga_beli'],
                    'margin' => $itemData['margin'],
                    'harga_jual_satuan' => $itemData['harga_jual_satuan'],
                    'selisih_pagu' => $itemData['selisih_pagu'],
                    'total_harga' => $itemData['total_harga_jual'],
                    'status' => $itemData['status'],
                ]);
            }

            DB::commit();

            return response()->json(['message' => 'Preorder berhasil diproses'], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error("Error approvePreorder", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json(['message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    public function getItems($id)
    {
        $preorder = PreOrder::with('items.produk')->findOrFail($id);

        return response()->json([
            'items' => $preorder->items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'produk_id' => $item->produk_id,
                    'produk' => $item->produk->merek,
                    'qty' => $item->qty,
                    'satuan' => $item->satuan,
                    'pagu' => $item->pagu,
                    'merek' => $item->merek,
                    'harga' => $item->harga,
                    'harga_setelah_pajak' => $item->harga_setelah_pajak,
                    'margin' => $item->margin,
                    'harga_jual_satuan' => $item->harga_jual_satuan,
                    'status' => $item->status
                ];
            }),

        ]);
    }




    // public function destroy(Request $request)
    // {
    //     DB::beginTransaction();
    //     try {
    //         // Cari preorder berdasarkan ID
    //         $preorder = Preorder::find($request->id);
    //         if (!$preorder) {
    //             return response()->json(['status' => false, 'message' => 'Pesanan tidak ditemukan.'], 404);
    //         }

    //         // Hapus semua item order yang terkait
    //         ItemOrder::where('preorder_id', $preorder->id)->delete();

    //         // Hapus preorder
    //         $preorder->delete();

    //         DB::commit();
    //         return response()->json(['status' => true, 'message' => 'Pesanan berhasil dihapus.']);
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         Log::error('Gagal menghapus pesanan: ' . $e->getMessage());
    //         return response()->json(['status' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
    //     }
    // }
}
