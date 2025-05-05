<x-backend.dashboard>
    <div class="container-fluid px-4">
        <h1 class="mt-4">Permission Details</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('permissions.index') }}">Permissions</a></li>
            <li class="breadcrumb-item active">Permission Details</li>
        </ol>

        <div class="row">
            <div class="col-xl-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-key me-1"></i>
                        Permission Information
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">{{ $permission->name }}</h5>
                        <p class="text-muted">Permission ID: {{ $permission->id }}</p>
                        <p class="mb-0">Created at {{ $permission->created_at->format('M d, Y') }}</p>
                        <p>Updated at {{ $permission->updated_at->format('M d, Y') }}</p>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-cogs me-1"></i>
                        Actions
                    </div>
                    <div class="card-body">
                        <a href="{{ route('permissions.edit', $permission->id) }}" class="btn btn-primary mb-2 w-100">
                            <i class="fas fa-edit me-1"></i> Edit Permission
                        </a>
                        
                        @php
                            $systemPermissions = ['view users', 'create users', 'edit users', 'delete users', 
                                'view roles', 'create roles', 'edit roles', 'delete roles', 
                                'view permissions', 'create permissions', 'edit permissions', 'delete permissions'];
                        @endphp
                        
                        @if(!in_array($permission->name, $systemPermissions))
                            <form action="{{ route('permissions.destroy', $permission->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger mb-2 w-100" onclick="return confirm('Are you sure you want to delete this permission?')">
                                    <i class="fas fa-trash me-1"></i> Delete Permission
                                </button>
                            </form>
                        @endif
                        
                        <a href="{{ route('permissions.index') }}" class="btn btn-secondary w-100">
                            <i class="fas fa-arrow-left me-1"></i> Back to List
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-user-tag me-1"></i>
                        Roles With This Permission
                    </div>
                    <div class="card-body">
                        @if($permission->roles->count() > 0)
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Role Name</th>
                                            <th>Users</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($permission->roles as $role)
                                            <tr>
                                                <td>{{ $role->id }}</td>
                                                <td>{{ $role->name }}</td>
                                                <td>
                                                    <span class="badge bg-primary">{{ $role->users->count() }}</span>
                                                </td>
                                                <td>
                                                    <a href="{{ route('roles.show', $role->id) }}" class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-eye"></i> View
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-warning mb-0">
                                <i class="fas fa-exclamation-triangle me-1"></i>
                                This permission is not assigned to any roles.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-backend.dashboard>