<?php

namespace App\Http\Controllers;


use App\Models\Barang;
use Illuminate\Http\Request;
use App\Imports\BarangImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;

class BarangController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $barang = Barang::query();

        if ($search = $request->search) {
            $barang->where('nama', 'like', "%{$search}%");
        }

        $barang = $barang->orderBy('nama', 'asc')->paginate(10);

        // Supaya query string (search, sort) tetap nempel waktu pindah halaman
        $barang->appends($request->all());

        return view('barang.list', [
            'barang' => $barang
        ]);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('barang.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [

            'nama' => 'required|max:255|string'
        ]);
        if ($validator->passes()) {
            $barang = new Barang();
            $barang->nama = $request->nama;
            $barang->save();
            return redirect()->route('barang.index')->with('success', 'Data berhasil ditambahkan');
        } else {
            return redirect()->route('barang.create')->with('error', 'Data gagal ditambahkan');
        }
    }



    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $barang = Barang::findOrFail($id);
        return view('barang.edit', compact('barang'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $barang = Barang::findOrFail($request->id);
        $validator = Validator::make($request->all(), [

            'nama' => 'required|max:255|string'
        ]);
        if ($validator->passes()) {
            $barang->nama = $request->nama;
            $barang->save();
            return redirect()->route('barang.index')->with('success', 'Data berhasil update');
        } else {
            return redirect()->route('barang.edit', $barang->$id)->withInput()->withErrors($validator);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $barang = Barang::findOrFail($request->id);
        if (!$barang) {
            session()->flash('error', 'data tidak di temukan');
            return response()->json([
                'status' => false
            ]);
        }
        $barang->delete();
        session()->flash('success', 'barang berhasil di hapus');
        return response()->json([
            'status' => true
        ]);
    }

    public function import()
    {
        return view('barang.import');
    }
    public function storeImport(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv',
        ]);

        try {
            Excel::import(new BarangImport, $request->file('file'));

            return redirect()->route('produk.index')->with('produk berhasil di import');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengimpor produk: ' . $e->getMessage());
        }
    }
}
