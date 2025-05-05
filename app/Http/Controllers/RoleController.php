<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    /**
     * Display a listing of the roles.
     */
    public function index()
    {
        $roles = Role::withCount('users', 'permissions')->paginate(10);
        return view('backend.roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new role.
     */
    public function create()
    {
        $permissions = Permission::all();
        return view('backend.roles.create', compact('permissions'));
    }

    /**
     * Store a newly created role in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:roles,name',
            'permissions' => 'nullable|array',
        ]);

        // Create the role
        $role = Role::create(['name' => $request->name]);
        
        // Assign permissions to the role
        if ($request->has('permissions')) {
            $role->givePermissionTo($request->permissions);
        }

        return redirect()->route('roles.index')
            ->with('success', 'Role created successfully.');
    }

    /**
     * Display the specified role.
     */
    public function show(Role $role)
    {
        $role->load('permissions');
        $usersCount = $role->users()->count();
        
        return view('backend.roles.show', compact('role', 'usersCount'));
    }

    /**
     * Show the form for editing the specified role.
     */
    public function edit(Role $role)
    {
        $permissions = Permission::all();
        $rolePermissions = $role->permissions->pluck('id')->toArray();
        
        return view('backend.roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    /**
     * Update the specified role in storage.
     */
    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|string|unique:roles,name,' . $role->id,
            'permissions' => 'nullable|array',
        ]);

        // Prevent modification of system roles
        if (in_array($role->name, ['System', 'Super Admin']) && !auth()->user()->hasRole('System')) {
            return redirect()->route('roles.index')
                ->with('error', 'You do not have permission to modify system roles.');
        }

        // Update role name
        $role->update(['name' => $request->name]);
        
        // Sync permissions
        $permissions = $request->permissions ?? [];
        $role->syncPermissions($permissions);

        return redirect()->route('roles.index')
            ->with('success', 'Role updated successfully.');
    }

    /**
     * Remove the specified role from storage.
     */
    public function destroy(Role $role)
    {
        // Prevent deletion of essential roles
        if (in_array($role->name, ['System', 'Super Admin', 'Administrator', 'Staff', 'Student'])) {
            return redirect()->route('roles.index')
                ->with('error', 'Cannot delete essential system roles.');
        }

        // Check if the role has users
        $usersCount = $role->users()->count();
        if ($usersCount > 0) {
            return redirect()->route('roles.index')
                ->with('error', "Cannot delete role. It has {$usersCount} users assigned.");
        }

        $role->delete();

        return redirect()->route('roles.index')
            ->with('success', 'Role deleted successfully.');
    }
}
