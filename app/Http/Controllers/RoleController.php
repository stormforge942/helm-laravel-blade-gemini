<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

class RoleController extends Controller
{
    //
    public function index()
    {
        $roles = Role::with('permissions')->get(); // Fetch roles and their associated permissions
        return view('roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::all();
        return view('roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $role = Role::create(['name' => $request->name]);
        $role->syncPermissions($request->permissions);

        return redirect()->route('roles.index')->with('success', 'Role created successfully.');
    }

    public function edit(Role $role)
    {
        $permissions = Permission::all();
        return view('roles.edit', compact('role', 'permissions'));
    }

    public function update(Request $request, Role $role)
    {
        $role->update(['name' => $request->name]);
        $role->syncPermissions($request->permissions);

        return redirect()->route('roles.index')->with('success', 'Role updated successfully.');
    }

    public function destroy(Role $role)
    {
        $role->delete();
        return back()->with('success', 'Role deleted successfully.');
    }

}
