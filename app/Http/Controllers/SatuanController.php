<?php

namespace App\Http\Controllers;

use App\Models\Satuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SatuanController extends Controller
{
    public function index()
    {
        $satuan = Satuan::orderBy('name', 'asc')->paginate(10);
        return view('satuan.list', [
            'satuan' => $satuan
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('satuan.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ]);

        if ($validator->passes()) {
            $satuan = new Satuan();
            $satuan->name = $request->name;
            $satuan->save();
            return redirect()->route('satuan.index')->with('success', 'Satuan berhasil ditambahkan');
        } else {
            return redirect()->route('satuan.create')->withInput()->withErrors($validator);
        }
    }



    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $satuan = Satuan::findOrFail($id);
        return view('satuan.edit', [
            'satuan' => $satuan
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $satuan = Satuan::findOrFail($id);
        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ]);

        if ($validator->passes()) {
            $satuan->name = $request->name;
            $satuan->save();
            return redirect()->route('satuan.index')->with('success', 'Satuan berhasil di update');
        } else {
            return redirect()->route('satuan.edit', $satuan->id)->withInput()->withErrors($validator);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $satuan = Satuan::findOrFail($request->id);
        if (!$satuan) {
            session()->flash('error', 'Data tidak di temukan');
            return response()->json([
                'status' => false
            ]);
        }
        $satuan->delete();
        session()->flash('success', 'Data berhasil dihapus');
        return response()->json([
            'status' => true
        ]);
    }
}
