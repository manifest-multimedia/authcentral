<x-backend.dashboard>
    <div class="container-fluid px-4">
        <h1 class="mt-4">Edit Permission</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('permissions.index') }}">Permissions</a></li>
            <li class="breadcrumb-item active">Edit Permission</li>
        </ol>

        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-key me-1"></i>
                Edit Permission Information
            </div>
            <div class="card-body">
                @php
                    $systemPermissions = ['view users', 'create users', 'edit users', 'delete users', 
                        'view roles', 'create roles', 'edit roles', 'delete roles', 
                        'view permissions', 'create permissions', 'edit permissions', 'delete permissions'];
                    $isSystemPermission = in_array($permission->name, $systemPermissions);
                @endphp
                
                @if($isSystemPermission)
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-1"></i>
                        <strong>Warning:</strong> This is a system permission. Modifying it may affect system functionality.
                    </div>
                @endif

                <form action="{{ route('permissions.update', $permission->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">Permission Name</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $permission->name) }}" required {{ $isSystemPermission && !auth()->user()->hasRole('System') ? 'disabled' : '' }}>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-0">
                        @if(!$isSystemPermission || auth()->user()->hasRole('System'))
                            <button type="submit" class="btn btn-primary">Update Permission</button>
                        @endif
                        <a href="{{ route('permissions.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-backend.dashboard>