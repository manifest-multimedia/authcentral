<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

class UserManagement extends Component
{
    use WithPagination;

    public $editUserId = null; // Track the user being edited
    public $name, $email, $role;
    public $roles = ['User', 'Admin', 'Super Admin', 'Manager', 'Tutor', 'Student', 'Parent', 'Alumni']; // Updated roles
    public $successMessage, $errorMessage;
    public $originalEmail;

    // Search and filter properties
    public $search = '';
    public $roleFilter = '';
    public $perPage = 15;

    protected $queryString = [
        'search' => ['except' => ''],
        'roleFilter' => ['except' => ''],
    ];

    protected $paginationTheme = 'bootstrap';

    protected function rules()
    {
        $rules = [
            'name' => 'required|string|min:3',
            'role' => 'required|in:User,Admin,Super Admin,Manager,Tutor,Student,Parent,Alumni',
        ];

        // Only validate email uniqueness if it has changed
        if ($this->email !== $this->originalEmail) {
            $rules['email'] = 'required|email|unique:users,email';
        } else {
            $rules['email'] = 'required|email';
        }

        return $rules;
    }

    public function mount()
    {
        // Initialize roles from database
        $this->loadAvailableRoles();
    }

    public function loadAvailableRoles()
    {
        // Get all roles from Spatie Permission
        $spatieRoles = Role::all()->pluck('name')->toArray();
        
        // Merge with hardcoded roles if needed
        $this->roles = !empty($spatieRoles) 
            ? $spatieRoles 
            : ['User', 'Admin', 'Super Admin', 'Manager', 'Tutor', 'Student', 'Parent', 'Alumni'];
    }

    public function getUsers()
    {
        $query = User::query();

        // Search by name or email
        if ($this->search) {
            $query->where(function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        }

        // Filter by role (checking both role column and Spatie roles)
        if ($this->roleFilter) {
            $query->where(function($q) {
                $q->where('role', $this->roleFilter)
                  ->orWhereHas('roles', function($roleQuery) {
                      $roleQuery->where('name', $this->roleFilter);
                  });
            });
        }

        return $query->orderBy('created_at', 'desc')->paginate($this->perPage);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingRoleFilter()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->reset(['search', 'roleFilter']);
        $this->resetPage();
    }

    public function edit($userId)
    {
        $this->editUserId = $userId;
        $user = User::findOrFail($userId);
        $this->name = $user->name;
        $this->email = $user->email;
        $this->role = $user->role;
        $this->originalEmail = $user->email; // Save the original email for validation
    }

    public function save()
    {
        $this->validate();

        try {
            $user = User::findOrFail($this->editUserId);
            $user->name = $this->name;
            $user->email = $this->email;
            $user->role = $this->role;
            
            // Sync Spatie role if using Spatie Permission
            if (method_exists($user, 'syncRoles')) {
                $user->syncRoles([$this->role]);
            }
            
            $user->save(); // Save changes to the database

            $this->successMessage = 'User updated successfully!';
            $this->editUserId = null; // Exit edit mode
        } catch (\Exception $e) {
            $this->errorMessage = 'Failed to update user: ' . $e->getMessage();
        }
    }

    public function delete($userId)
    {
        // Prevent the logged-in user from deleting their own account
        if ($userId === Auth::id()) {
            $this->errorMessage = 'You cannot delete your own account.';
            return;
        }

        try {
            DB::beginTransaction();
            User::findOrFail($userId)->delete();
            DB::commit();

            $this->successMessage = 'User deleted successfully!';
        } catch (\Exception $e) {
            DB::rollBack();
            $this->errorMessage = 'Failed to delete user: ' . $e->getMessage();
        }
    }

    public function cancelEdit()
    {
        $this->editUserId = null; // Exit edit mode
        $this->reset(['name', 'email', 'role']); // Reset form fields
    }

    public function render()
    {
        return view('livewire.user-management', [
            'users' => $this->getUsers(),
        ]);
    }
}
