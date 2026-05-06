<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Cache;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\Module;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        $query = Role::with('permissions');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        $roles = $query->get();
        return response()->json($roles);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name'
        ]);

        $role = Role::create([
            'name' => $request->name,
            'guard_name' => 'web'
        ]);

        $role->syncPermissions($request->permissions ?? []);
        Cache::forget('users-dropdown-data'); //Being used in User Controller

        return response()->json(['message' => 'Role created successfully.', 'role' => $role], 201);
    }

    public function show(Role $role)
    {
        $data['role'] = Role::with('permissions')->find($role->id);
        return response()->json($data);
    }

    public function update(Request $request, Role $role)
    {
        $role->update(['name' => $request->name]);
        $role->syncPermissions($request->permissions ?? []);
        Cache::forget('users-dropdown-data'); //Being used in User Controller
        return response()->json(['message' => 'Role updated successfully.', 'role' => $role]);
    }

    public function destroy(Role $role)
    {
        // Optional: prevent deleting super admin role
        if ($role->name === 'Super Admin') {
            return response()->json(['message' => 'Cannot delete Super Admin role.'], 403);
        }

        $role->delete();
        Cache::forget('users-dropdown-data'); //Being used in User Controller
        return response()->json(['message' => 'Role deleted successfully.']);
    }

    public function getModules()
    {
        $modules = Module::all();
        $additionalPermissions = Permission::whereNull('module_id')->select(['id', 'name', 'guard_name'])->get();
        return response()->json([
            'modules' => $modules,
            'additionalPermissions' => $additionalPermissions
        ]);
    }

    public function getRoles()
    {
        $roles = Role::where('name', '!=', 'Super Admin')->get();
        return response()->json($roles);
    }
}
