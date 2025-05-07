<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Validator;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class PermissionController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:view permission', only: ['index']),
            new Middleware('permission:create permission', only: ['create']),
            new Middleware('permission:edit permission', only: ['edit']),
            new Middleware('permission:delete permission', only: ['delete']),
        ];
    }

    public function index()
    {
        $permissions = Permission::orderBy('created_at', 'desc')->paginate(10);
        return view('permission.list', [
            'permissions' => $permissions
        ]);
    }
    //menambahkan permission di halaman
    public function create()
    {

        return view('permission.create');
    }
    //menyimpan permission
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:permissions|min:3'
        ]);

        if ($validator->passes()) {
            Permission::create(['name' => $request->name]);
            return redirect()->route('permission.index')->with('success', 'Permission berhasil ditambahkan');
        } else {
            return redirect()->route('permission.create')->withInput()->withErrors($validator);
        }
    }
    //mnegdit permission
    public function edit($id)
    {
        $permission = Permission::findOrFail($id);
        return view('permission.edit', [
            'permission' => $permission
        ]);
    }
    //update permission
    public function update($id, Request $request)
    {
        $permission = Permission::findOrFail($id);
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:3|unique:permissions,name,' . $id . ',id'
        ]);

        if ($validator->passes()) {
            //Permission::create(['name' => $request->name]);
            $permission->name = $request->name;
            $permission->save();
            return redirect()->route('permission.index')->with('success', 'Permission berhasil di perbaruhi');
        } else {
            return redirect()->route('permission.edit', $id)->withInput()->withErrors($validator);
        }
    }
    //hapus permission
    public function destroy(Request $request)
    {
        $id = $request->id;
        $permission = Permission::find($id);

        if ($permission == null) {
            session()->flash('error', 'Permission tidak di temukan ');
            return response()->json([
                'status' => false
            ]);
        }
        $permission->delete();
        session()->flash('success', 'permission berhasil di hapus');
        return response()->json([
            'status' =>  true
        ]);
    }
}
