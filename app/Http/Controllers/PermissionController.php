<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    /**
     * Display a listing of permissions.
     */
    public function index()
    {
        $permissions = Permission::paginate(15);
        return view('backend.permissions.index', compact('permissions'));
    }

    /**
     * Show the form for creating a new permission.
     */
    public function create()
    {
        return view('backend.permissions.create');
    }

    /**
     * Store a newly created permission in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:permissions,name',
        ]);

        Permission::create(['name' => $request->name]);

        return redirect()->route('permissions.index')
            ->with('success', 'Permission created successfully.');
    }

    /**
     * Display the specified permission.
     */
    public function show(Permission $permission)
    {
        $permission->load('roles');
        return view('backend.permissions.show', compact('permission'));
    }

    /**
     * Show the form for editing the specified permission.
     */
    public function edit(Permission $permission)
    {
        return view('backend.permissions.edit', compact('permission'));
    }

    /**
     * Update the specified permission in storage.
     */
    public function update(Request $request, Permission $permission)
    {
        $request->validate([
            'name' => 'required|string|unique:permissions,name,' . $permission->id,
        ]);

        // Additional check for system permissions
        $systemPermissions = ['view users', 'create users', 'edit users', 'delete users', 
            'view roles', 'create roles', 'edit roles', 'delete roles', 
            'view permissions', 'create permissions', 'edit permissions', 'delete permissions'];
            
        if (in_array($permission->name, $systemPermissions) && !auth()->user()->hasRole('System')) {
            return redirect()->route('permissions.index')
                ->with('error', 'You do not have permission to modify system permissions.');
        }

        $permission->update(['name' => $request->name]);

        return redirect()->route('permissions.index')
            ->with('success', 'Permission updated successfully.');
    }

    /**
     * Remove the specified permission from storage.
     */
    public function destroy(Permission $permission)
    {
        // Prevent deletion of essential permissions
        $systemPermissions = ['view users', 'create users', 'edit users', 'delete users', 
            'view roles', 'create roles', 'edit roles', 'delete roles', 
            'view permissions', 'create permissions', 'edit permissions', 'delete permissions'];
            
        if (in_array($permission->name, $systemPermissions)) {
            return redirect()->route('permissions.index')
                ->with('error', 'Cannot delete essential system permissions.');
        }

        // Check if permission is assigned to any roles
        $rolesCount = $permission->roles()->count();
        if ($rolesCount > 0) {
            return redirect()->route('permissions.index')
                ->with('error', "Cannot delete permission. It is used by {$rolesCount} roles.");
        }

        $permission->delete();

        return redirect()->route('permissions.index')
            ->with('success', 'Permission deleted successfully.');
    }
}
