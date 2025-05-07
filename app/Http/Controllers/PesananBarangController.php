<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\Satuan;
use App\Models\Approved;
use App\Models\Costumer;
use App\Models\Kategori;
use App\Models\Preorder;
use App\Models\Supplier;
use App\Models\ItemOrder;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PesananBarangController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $no_spo = 'ORD-' . now()->format('Ymd') . '-' . rand(1000, 9999);
        $order_date = now()->format('Y-m-d H:i:s');
        $customers = Costumer::orderBy('name','asc')->get();
        $satuan = Satuan::all();
        $supplier = Supplier::all();
        $kategori = Kategori::all();
        $produk = Produk::with(['satuan', 'kategori', 'supplier', 'barang'])->get();
        $is_lanjutan = false;

        // Menambahkan is_lanjutan sebagai false
        return view('pesanan.create', compact('no_spo', 'order_date', 'customers', 'satuan', 'supplier', 'produk', 'kategori', 'is_lanjutan'));
    }

    public function getProductList()
    {
        $produk = Produk::with(['satuan', 'kategori'])->get(); // Pastikan kategori dan satuan dimuat

        return response()->json($produk);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create() {}

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'no_spo' => 'required|unique:preorders,no_spo',
            'name_cust' => 'required|exists:costumers,id',
            'no_surat' => 'nullable|string',
            'order_date' => 'required|date',
            'products' => 'required|json',
            'parent_id' => 'nullable|exists:preorders,id', // ← ini ditambahin
        ]);

        try {
            DB::beginTransaction();

            // Simpan data utama ke preorders
            $preorder = Preorder::create([
                'no_spo' => $data['no_spo'],
                'name_cust' => $data['name_cust'],
                'no_surat' => $data['no_surat'],
                'order_date' => $data['order_date'],
                'status' => 'pending order',
                'parent_id' => $data['parent_id'] ?? null, // ← ini yang penting buat order lanjutan
            ]);

            // Decode JSON produk dan cek apakah ada isinya
            $products = json_decode($data['products'], true);
            if (empty($products)) {
                throw new \Exception("Daftar produk tidak boleh kosong.");
            }

            $margin = 0.7; // Simpan margin dalam variabel
            foreach ($products as $product) {
                if (!isset($product['produk_id']) || !is_numeric($product['produk_id'])) {
                    throw new \Exception("Produk tidak valid atau tidak memiliki ID.");
                }

                // Ambil supplier_id dari produk (pastikan dikirim dari frontend)
                $supplierId = $product['supplier_id'] ?? null;

                // Default PPN 11%
                $ppn = 11;

                // Kalau supplier ada, ambil PPN-nya dari database
                if ($supplierId) {
                    $supplier = Supplier::find($supplierId);
                    if ($supplier) {
                        $ppn = $supplier->pajak ?? 11; // fallback ke 11% kalau null
                    }
                }

                $qty = $product['qty'] ?? 1;
                $harga = $product['harga'] ?? 0;
                $pagu = $product['pagu'] ?? 0;

                ItemOrder::create([
                    'preorder_id' => $preorder->id,
                    'produk_id' => $product['produk_id'],
                    'qty' => $qty,
                    'pagu' => $pagu,
                    'total_pagu' => $pagu * $qty,
                    'harga' => $harga,
                    'total_harga' => $harga * $qty,
                    'margin' => $margin,
                    'ppn' => $ppn, // ← Ambil dari supplier
                    'status' => 'pending',
                ]);
            }


            DB::commit();
            return redirect()->route('pesanan.index')->with('success', 'Pesanan berhasil disimpan');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal menyimpan pesanan: ' . $e->getMessage()]);
        }
    }

   
}
