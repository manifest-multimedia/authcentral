<x-backend.dashboard>
    <div class="container-fluid px-4">
        <h1 class="mt-4">Role Details</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('roles.index') }}">Roles</a></li>
            <li class="breadcrumb-item active">Role Details</li>
        </ol>

        <div class="row">
            <div class="col-xl-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-user-tag me-1"></i>
                        Role Information
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">{{ $role->name }}</h5>
                        <p class="text-muted">Role ID: {{ $role->id }}</p>
                        <p class="mb-0">Created at {{ $role->created_at->format('M d, Y') }}</p>
                        <p>Updated at {{ $role->updated_at->format('M d, Y') }}</p>
                        
                        <div class="mt-3">
                            <span class="d-block mb-2"><strong>Users with this role:</strong> {{ $usersCount }}</span>
                            <span class="d-block"><strong>Permissions assigned:</strong> {{ $role->permissions->count() }}</span>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-cogs me-1"></i>
                        Actions
                    </div>
                    <div class="card-body">
                        <a href="{{ route('roles.edit', $role->id) }}" class="btn btn-primary mb-2 w-100">
                            <i class="fas fa-edit me-1"></i> Edit Role
                        </a>
                        
                        @if(!in_array($role->name, ['System', 'Super Admin', 'Administrator', 'Staff', 'Student']))
                            <form action="{{ route('roles.destroy', $role->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger mb-2 w-100" onclick="return confirm('Are you sure you want to delete this role?')">
                                    <i class="fas fa-trash me-1"></i> Delete Role
                                </button>
                            </form>
                        @endif
                        
                        <a href="{{ route('roles.index') }}" class="btn btn-secondary w-100">
                            <i class="fas fa-arrow-left me-1"></i> Back to List
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-key me-1"></i>
                        Permissions
                    </div>
                    <div class="card-body">
                        @if($role->permissions->count() > 0)
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Name</th>
                                            <th>Created At</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($role->permissions as $permission)
                                            <tr>
                                                <td>{{ $permission->id }}</td>
                                                <td>{{ $permission->name }}</td>
                                                <td>{{ $permission->created_at->format('M d, Y') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-warning mb-0">
                                <i class="fas fa-exclamation-triangle me-1"></i>
                                No permissions assigned to this role.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-backend.dashboard>