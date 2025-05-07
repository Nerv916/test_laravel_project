<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use App\Imports\SupplierImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $supplier = Supplier::orderBy('name', 'asc')->paginate(10);
        return view('supplier.list', [
            'supplier' => $supplier
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('supplier.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_supplier' => 'required|integer',
            'name' => 'required|string',
            'alamat' => 'required|string',
            'pic' => 'required|string',
            'kontak' => 'required|string',
            'npwp' => 'required|string',
            'pajak' => 'required|numeric|min:0|max:100',
        ]);

        if ($validator->passes()) {
            $supplier = new Supplier();
            $supplier->id_supplier = $request->id_supplier;
            $supplier->name = $request->name;
            $supplier->alamat = $request->alamat;
            $supplier->pic = $request->pic;
            $supplier->kontak = $request->kontak;
            $supplier->npwp = $request->npwp;
            $supplier->pajak = $request->pajak;
            $supplier->save();

            return redirect()->route('supplier.index')->with('success', 'Supplier berhasil ditambahkan');
        } else {
            return redirect()->route('supplier.create')->withInput()->withErrors('error', 'Supplier gagal ditambahkan');
        }
    }





    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $supplier = Supplier::findOrFail($id);
        return view('supplier.edit', [
            'supplier' => $supplier
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $supplier = Supplier::findOrFail($id);
        $validator = Validator::make($request->all(), [
            'id_supplier' => 'required|integer',
            'name' => 'required|string',
            'alamat' => 'required|string',
            'pic' => 'required|string',
            'kontak' => 'required|string',
            'npwp' => 'required|string',
            'pajak' => 'required|numeric|min:0|max:100',
        ]);

        if ($validator->passes()) {
            $supplier->id_supplier = $request->id_supplier;
            $supplier->name = $request->name;
            $supplier->alamat = $request->alamat;
            $supplier->pic = $request->pic;
            $supplier->kontak = $request->kontak;
            $supplier->npwp = $request->npwp;
            $supplier->pajak = $request->pajak;
            $supplier->save();

            return redirect()->route('supplier.index')->with('success', 'Supplier berhasil perbarui');
        } else {
            return redirect()->route('supplier.edit', $id)->withInput()->withErrors('error', 'Supplier gagal perbarui');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $supplier = Supplier::findOrFail($request->id);
        if ($supplier === null) {
            session()->flash('error', 'Supplier gagal ditemukan');
            return response()->json([
                'status' => false
            ]);
        }
        $supplier->delete();
        session()->flash('success', 'supplier berhasil dihapus');
        return response()->json([
            'status' => true
        ]);
    }

    public function import()
    {
        return view('supplier.import');
    }

    public function storeImport(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv',
        ]);

        try {
            Excel::import(new SupplierImport, $request->file('file'));

            return redirect()->route('supplier.index')->with('Supplier berhasil di import');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengimpor produk: ' . $e->getMessage());
        }
    }
}
