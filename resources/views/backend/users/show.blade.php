<x-backend.dashboard>
    <div class="container-fluid px-4" style="margin-top:150px;">
        <h1 class="mt-4">User Details</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Users</a></li>
            <li class="breadcrumb-item active">User Details</li>
        </ol>

        <div class="row">
            <div class="col-xl-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <div class="card-title">
                        <i class="fas fa-user me-1"></i>
                        User Information
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-4">
                            <div class="rounded-circle bg-primary d-inline-flex align-items-center justify-content-center text-white mb-3" style="width: 100px; height: 100px;">
                                <span class="fs-1">{{ substr($user->name, 0, 1) }}</span>
                            </div>
                            <h5 class="card-title">{{ $user->name }}</h5>
                            <p class="card-text text-muted">{{ $user->email }}</p>
                        </div>
                        
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                ID
                                <span class="badge bg-primary rounded-pill">{{ $user->id }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Created
                                <span>{{ $user->created_at->format('M d, Y') }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Last Updated
                                <span>{{ $user->updated_at->format('M d, Y') }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <div class="card-title">
                        <i class="fas fa-user-tag me-1"></i>
                        Roles & Permissions
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <h5>Assigned Roles</h5>
                            <div>
                                @forelse($user->roles as $role)
                                    <span class="badge bg-info text-dark mb-1 me-1">{{ $role->name }}</span>
                                @empty
                                    <span class="text-muted">No roles assigned</span>
                                @endforelse
                            </div>
                        </div>
                        
                        <div>
                            <h5>Permissions</h5>
                            <p class="text-muted small">These permissions are inherited from the assigned roles</p>
                            
                            <div>
                                @php
                                    $permissions = $user->getAllPermissions();
                                @endphp
                                
                                @forelse($permissions as $permission)
                                    <span class="badge bg-light text-dark mb-1 me-1">{{ $permission->name }}</span>
                                @empty
                                    <span class="text-muted">No permissions available</span>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">
                        <i class="fas fa-cogs me-1"></i>
                        Actions</div>
                    </div>
                    <div class="card-body">
                        <a href="{{ route('users.edit', $user->id) }}" class="btn btn-primary">
                            <i class="fas fa-edit"></i> Edit User
                        </a>
                        
                        @if($user->id !== auth()->id())
                            <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="d-inline ms-2">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this user?')">
                                    <i class="fas fa-trash"></i> Delete User
                                </button>
                            </form>
                        @endif
                        
                        <a href="{{ route('users.index') }}" class="btn btn-secondary ms-2">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-backend.dashboard>