<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class UserManagement extends Component
{
    public $users;
    public $editUserId = null; // Track the user being edited
    public $name, $email, $role;
    public $roles = ['User', 'Admin', 'Super Admin', 'Manager', 'Tutor', 'Student', 'Parent', 'Alumni']; // Updated roles
    public $successMessage, $errorMessage;
    public $originalEmail;

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
            $user->save(); // Save changes to the database

            // Refresh the user list after saving
            $this->fetchUsers();

            $this->successMessage = 'User updated successfully!';
            $this->editUserId = null; // Exit edit mode
        } catch (\Exception $e) {
            $this->errorMessage = 'Failed to update user.';
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
