<div class="container mt-5">
    <h2>User Management</h2>

    <!-- Display Success and Error Messages -->
    @if ($successMessage)
        <div class="alert alert-success">{{ $successMessage }}</div>
    @endif

    @if ($errorMessage)
        <div class="alert alert-danger">{{ $errorMessage }}</div>
    @endif

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
                @foreach ($users as $user)
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
                            <td>{{ ucfirst($user->role) }}</td>
                            <td>
                                <button wire:click="edit({{ $user->id }})" class="btn btn-primary btn-sm">Edit</button>
                                <button wire:click="delete({{ $user->id }})" class="btn btn-danger btn-sm">Delete</button>
                            </td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
