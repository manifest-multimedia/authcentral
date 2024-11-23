<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class UserManagement extends Component
{
    public $users;
    public $editUserId = null; // Track the user being edited
    public $name, $email, $role;
    public $roles = ['admin', 'user', 'tutor', 'student', 'manager'];
    public $successMessage, $errorMessage;

    protected $rules = [
        'name' => 'required|string|min:3',
        'email' => 'required|email|unique:users,email',
        'role' => 'required|in:admin,user,manager',
    ];

    public function mount()
    {
        $this->fetchUsers();
    }

    public function fetchUsers()
    {
        $this->users = User::all();
    }

    public function edit($userId)
    {
        $this->editUserId = $userId;
        $user = User::findOrFail($userId);
        $this->name = $user->name;
        $this->email = $user->email;
        $this->role = $user->role;
    }

    public function save()
    {
        $this->validate();

        try {
            $user = User::findOrFail($this->editUserId);
            $user->update([
                'name' => $this->name,
                'email' => $this->email,
                'role' => $this->role,
            ]);

            $this->successMessage = 'User updated successfully!';
            $this->editUserId = null; // Exit edit mode
            $this->fetchUsers(); // Refresh the user list
        } catch (\Exception $e) {
            $this->errorMessage = 'Failed to update user.';
        }
    }

    public function delete($userId)
    {
        try {
            DB::beginTransaction();
            User::findOrFail($userId)->delete();
            DB::commit();

            $this->successMessage = 'User deleted successfully!';
            $this->fetchUsers();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->errorMessage = 'Failed to delete user.';
        }
    }

    public function cancelEdit()
    {
        $this->editUserId = null; // Exit edit mode
        $this->reset(['name', 'email', 'role']); // Reset form fields
    }

    public function render()
    {
        return view('livewire.user-management');
    }
}
