<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use Illuminate\Http\Request;
use App\Imports\KategoriImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;

class KategoriController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $kategori = Kategori::orderBy('name', 'asc')->paginate(10);
        return view('kategori.list', [
            'kategori' => $kategori
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('kategori.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
        ]);

        if ($validator->passes()) {
            $kategori = new Kategori();
            $kategori->name = $request->name;
            $kategori->save();

            return redirect()->route('kategori.index')->with('success', 'Kategori berhasil ditambahkan');
        } else {
            return redirect()->route('kategori.create')->withInput()->withErrors($validator);
        };
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $kategori = Kategori::findOrFail($id);
        return view('kategori.edit', [
            'kategori' => $kategori
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $kategori = Kategori::findOrFail($id);
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
        ]);

        if ($validator->passes()) {

            $kategori->name = $request->name;
            $kategori->save();

            return redirect()->route('kategori.index')->with('success', 'Kategori berhasil di perbarui');
        } else {
            return redirect()->route('kategori.edit', $id)->withInput()->withErrors($validator);
        };
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $kategori = Kategori::findOrFail($request->id);
        if ($kategori === null) {
            session()->flash('error', 'Kategori tidak ditemukan');
            return response()->json([
                'status' => false
            ]);
        }

        $kategori->delete();
        session()->flash('success', 'Kategori berhasil dihapus');
        return response()->json([
            'status' => true
        ]);
    }

    public function import()
    {
        return view('kategori.import');
    }

    public function storeImport(Request $request)
    {
        $excel = Excel::import(new KategoriImport, $request->file('file'));
        if (!$excel) {
            return redirect()->withErrors($request);
        }
        return redirect()->route('kategori.index')->with('success, kategori berhasil di tambahkan');
    }
}
