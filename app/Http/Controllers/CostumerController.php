<?php

namespace App\Http\Controllers;

use App\Models\Costumer;
use Illuminate\Http\Request;
use App\Imports\CostumerImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;


class CostumerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $costumer = Costumer::orderBy('name','asc')->paginate(10);
        return view('costumer.list', [
            'costumer' => $costumer
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('costumer.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'alamat' => 'required',
            'kontak' => 'required',
            'pic' => 'required',
            'npwp' => 'required',
        ]);

        if ($validator->passes()) {
            $costumer = new Costumer();
            $costumer->name = $request->name;
            $costumer->alamat = $request->alamat;
            $costumer->kontak = $request->kontak;
            $costumer->pic = $request->pic;
            $costumer->npwp = $request->npwp;
            $costumer->save();

            return redirect()->route('costumer.index')->with('success', 'Costumer berhasil di tambahkan');
        } else {
            return redirect()->route('costumer.create')->withInput()->withErrors($validator);
        }
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $costumer = Costumer::findOrFail($id);
        return view('costumer.edit', [
            'costumer' => $costumer
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $costumer = Costumer::findOrFail($id);
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'alamat' => 'required',
            'kontak' => 'required|min:12',
            'pic' => 'required',
            'npwp' => 'required|min:15|max:16',
        ]);

        if ($validator->passes()) {

            $costumer->name = $request->name;
            $costumer->alamat = $request->alamat;
            $costumer->kontak = $request->kontak;
            $costumer->pic = $request->pic;
            $costumer->npwp = $request->npwp;
            $costumer->save();

            return redirect()->route('costumer.index')->with('success', 'Costumer berhasil di tambahkan');
        } else {
            return redirect()->route('costumer.edit', $id)->withInput()->withErrors($validator);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $costumer = Costumer::find($request->id);

        if (!$costumer) {
            return response()->json([
                'status' => false,
                'message' => 'Data costumer tidak ditemukan'
            ], 404);
        }

        $costumer->delete();

        return response()->json([
            'status' => true,
            'message' => 'Data costumer berhasil dihapus'
        ]);
    }

    public function import()
    {
        return view('costumer.import');
    }

    public function storeImport(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv',
        ]);

        try {
            Excel::import(new CostumerImport, $request->file('file'));

            return redirect()->route('costumer.index')->with('Cust berhasil di import');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengimpor produk: ' . $e->getMessage());
        }

        
    }
}
