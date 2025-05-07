<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Produk;
use App\Models\Satuan;
use App\Models\Kategori;
use App\Models\Supplier;
use Illuminate\Http\Request;
use App\Imports\ProdukImport;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;

class ProdukController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Produk::query();

        if ($search = $request->search) {
            $query->whereHas('barang', function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%");
            })->orWhere('merek', 'like', "%{$search}%")
                ->orWhereHas('kategori', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                })
                ->orWhereHas('satuan', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                })
                ->orWhere('nie', 'like', "%{$search}%");
        }

        $produk = $query->orderBy('id', 'desc')->paginate(10);

        return view('produk.list', [
            'produk' => $produk
        ]);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('produk.create', [
            'kategoris' => Kategori::orderBy('name', 'asc')->get(),
            'satuans' => Satuan::orderBy('name', 'asc')->get(),
            'suppliers' => Supplier::orderBy('name', 'asc')->get(),
            'barangs' => Barang::orderBy('nama', 'asc')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'barang_id' => 'required|exists:barangs,id',
            'merek' => 'required|string|max:255',
            'kategori_id' => 'required|exists:kategoris,id',
            'satuan_id' => 'required|exists:satuans,id',
            'supplier_id' => 'required|exists:suppliers,id', // Pastikan ID supplier valid
            'nie' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            Log::error('Validation failed', $validator->errors()->all());
            return redirect()->route('produk.create')->withInput()->withErrors($validator);
        }

        // Simpan produk dengan supplier_id dari request
        try {
            $produk = Produk::create([
                'barang_id' => $request->barang_id,
                'merek' => $request->merek,
                'kategori_id' => $request->kategori_id,
                'supplier_id' => $request->supplier_id,
                'satuan_id' => $request->satuan_id,
                'nie' => $request->nie,
            ]);
        } catch (\Exception $e) {
            Log::error('Error saving product: ' . $e->getMessage());
            return redirect()->route('produk.create')->with('error', 'Error saving product.');
        }

        return redirect()->route('produk.index')->with('success', 'Produk berhasil ditambahkan');
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {

        $produk = Produk::findOrFail($id);
        $kategoris = Kategori::orderBy('name', 'asc')->get();
        $satuans = Satuan::orderBy('name', 'asc')->get();
        $suppliers = Supplier::orderBy('name', 'asc')->get();
        $barangs = Barang::orderBy('nama', 'asc')->get();

        return view('produk.edit', compact('produk', 'kategoris', 'satuans', 'suppliers', 'barangs'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $produk = Produk::findOrFail($id);
        // dd($produk);

        // Validasi input
        $validator = Validator::make($request->all(), [
            'barang_id' => 'required|exists:barangs,id',
            'merek' => 'required|string|max:255',
            'kategori_id' => 'required|exists:kategoris,id',
            'satuan_id' => 'required|exists:satuans,id',
            'supplier_id' => 'required|exists:suppliers,id', // Pastikan supplier ID valid
            'nie' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->route('produk.edit', $id)->withInput()->withErrors($validator);
        }

        // Update data produk
        try {
            $produk->update([
                'barang_id' => $request->barang_id,
                'merek' => $request->merek,
                'kategori_id' => $request->kategori_id,
                'supplier_id' => $request->supplier_id,
                'satuan_id' => $request->satuan_id,
                'nie' => $request->nie,
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating product: ' . $e->getMessage());
            return redirect()->route('produk.edit', $id)->with('error', 'Gagal memperbarui produk.');
        }

        return redirect()->route('produk.index')->with('success', 'Produk berhasil diperbarui');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $produk = Produk::findOrFail($request->id);
        if ($produk === null) {
            session()->flash('error', 'produk tidak di temukan');
            return response()->json(
                ['status' => false]
            );
        }
        $produk->delete();
        session()->flash('succes', 'produk berhasil di hapus');
        return response()->json([
            'status' => true
        ]);
    }
    public function import()
    {
        return view('produk.import');
    }

    public function storeImport(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv',
        ]);

        try {
            Excel::import(new ProdukImport, $request->file('file'));

            return redirect()->route('produk.index')->with('produk berhasil di import');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengimpor produk: ' . $e->getMessage());
        }
    }
}
