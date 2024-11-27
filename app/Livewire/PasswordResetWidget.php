<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class PasswordResetWidget extends Component
{
    public $email;
    public $password;
    public $password_confirmation;
    public function render()
    {
        return view('livewire.password-reset-widget');
    }

    public function resetPassword()
    {
        $this->validate(
            [
                'email' => 'required|email',
                'password' => 'required|confirmed'
            ]
        );

        $user = User::where('email', $this->email)->first();

        if ($user) {
            $user->password = Hash::make($this->password);
            $user->save();
            // Flash Response 
            session()->flash('success', 'Password has been reset successfully');
        } else {
            // Flash Response 
            session()->flash('error', 'No existing account found for this email address.');
        }
    }
}
