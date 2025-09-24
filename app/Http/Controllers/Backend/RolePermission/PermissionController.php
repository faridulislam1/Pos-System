<?php

namespace App\Http\Controllers\Backend\RolePermission;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    
    public function index()
    {
        $permissions = Permission::all();

        return view('backend.settings.permission.index', compact('permissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:permissions',
        ]);

        try {
            $name = slugify($request->name);
            if ($request->type == 1) {
                Permission::create([
                    'name' => $name
                ]);
                return back()->with('success', 'Permission added');
            } else {
                // Resource Permission create
                Permission::create(['name' => 'view-' . $name]);
                Permission::create(['name' => 'add-' . $name]);
                Permission::create(['name' => 'edit-' . $name]);
                Permission::create(['name' => 'delete-' . $name]);
                return back()->with('success', 'Resource permission added');
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Something went wrong');
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => "required|unique:permissions,name," . $id
        ]);

        if ($data = Permission::findOrFail($id)) {
            $data->update([
                'name' => $request->name
            ]);
            return back()->with('success', 'Permission has been updated');
        } else {
            return back()->with('error', 'Permission with id ' . $id . ' note found');
        }
    }

    public function destroy($id)
    {
        $data = Permission::findOrFail($id);
        $data->delete();

        return back()->with('success', 'Permission is deleted');
    }
}
