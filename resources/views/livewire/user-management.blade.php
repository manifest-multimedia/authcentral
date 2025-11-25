<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>User Management</h2>
        <div class="text-muted">
            {{ $users->total() }} {{ Str::plural('user', $users->total()) }}
        </div>
    </div>

    <!-- Display Success and Error Messages -->
    @if ($successMessage)
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ $successMessage }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if ($errorMessage)
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ $errorMessage }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <!-- Search and Filter Section -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3 mb-md-0">
                    <label for="search" class="form-label">Search</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="fas fa-search"></i>
                            </span>
                        </div>
                        <input 
                            type="text" 
                            wire:model.live.debounce.300ms="search" 
                            class="form-control" 
                            id="search"
                            placeholder="Search by name or email..."
                        >
                    </div>
                </div>
                <div class="col-md-4 mb-3 mb-md-0">
                    <label for="roleFilter" class="form-label">Filter by Role</label>
                    <select wire:model.live="roleFilter" class="form-control" id="roleFilter">
                        <option value="">All Roles</option>
                        @foreach ($roles as $roleOption)
                            <option value="{{ $roleOption }}">{{ ucfirst($roleOption) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button wire:click="clearFilters" class="btn btn-outline-secondary w-100">
                        <i class="fas fa-times"></i> Clear
                    </button>
                </div>
            </div>
            
            <!-- Loading Indicator -->
            <div wire:loading class="mt-3">
                <div class="alert alert-info mb-0">
                    <i class="fas fa-spinner fa-spin"></i> Loading...
                </div>
            </div>
        </div>
    </div>

    <!-- Users Table -->
    <div class="table-responsive">
        <table class="table table-striped table-bordered">
            <thead class="thead-dark">
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                    <tr>
                        @if ($editUserId === $user->id)
                            <!-- Edit Mode -->
                            <td>
                                <input type="text" wire:model="name" class="form-control" placeholder="Name">
                                @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                            </td>
                            <td>
                                <input type="email" wire:model="email" class="form-control" placeholder="Email">
                                @error('email') <small class="text-danger">{{ $message }}</small> @enderror
                            </td>
                            <td>
                                <select wire:model="role" class="form-control">
                                    <option value="">Select Role</option>
                                    @foreach ($roles as $roleOption)
                                        <option value="{{ $roleOption }}">{{ ucfirst($roleOption) }}</option>
                                    @endforeach
                                </select>
                                @error('role') <small class="text-danger">{{ $message }}</small> @enderror
                            </td>
                            <td>
                                <button wire:click="save" class="btn btn-success btn-sm">Save</button>
                                <button wire:click="cancelEdit" class="btn btn-secondary btn-sm">Cancel</button>
                                <button wire:click="delete({{ $user->id }})" class="btn btn-danger btn-sm">Delete</button>
                            </td>
                        @else
                            <!-- Display Mode -->
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @if($user->role)
                                    <span class="badge badge-primary">{{ ucfirst($user->role) }}</span>
                                @endif
                                @if(method_exists($user, 'getRoleNames') && $user->getRoleNames()->isNotEmpty())
                                    @foreach($user->getRoleNames() as $roleName)
                                        <span class="badge badge-secondary ml-1">{{ ucfirst($roleName) }}</span>
                                    @endforeach
                                @endif
                            </td>
                            <td>
                                <button wire:click="edit({{ $user->id }})" class="btn btn-primary btn-sm">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <button 
                                    wire:click="delete({{ $user->id }})" 
                                    class="btn btn-danger btn-sm"
                                    onclick="return confirm('Are you sure you want to delete this user?')"
                                >
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center py-4">
                            <div class="text-muted">
                                @if ($search || $roleFilter)
                                    <p class="mb-2">No users found matching your filters.</p>
                                    <button wire:click="clearFilters" class="btn btn-sm btn-primary">
                                        Clear Filters
                                    </button>
                                @else
                                    <p>No users available.</p>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-between align-items-center mt-3">
        <div class="text-muted">
            Showing {{ $users->firstItem() ?? 0 }} to {{ $users->lastItem() ?? 0 }} of {{ $users->total() }} results
        </div>
        <div>
            {{ $users->links() }}
        </div>
    </div>
</div>
