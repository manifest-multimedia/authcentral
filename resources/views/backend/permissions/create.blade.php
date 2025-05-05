<x-backend.dashboard>
    <div class="container-fluid px-4">
        <h1 class="mt-4">Create New Permission</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('permissions.index') }}">Permissions</a></li>
            <li class="breadcrumb-item active">Create New Permission</li>
        </ol>

        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-key me-1"></i>
                Permission Information
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-1"></i>
                    <strong>Tip:</strong> Permission names should be descriptive and follow the pattern: <code>action object</code> (e.g., "edit posts", "view users").
                </div>

                <form action="{{ route('permissions.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">Permission Name</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-0">
                        <button type="submit" class="btn btn-primary">Create Permission</button>
                        <a href="{{ route('permissions.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-backend.dashboard>