<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Hash;
use Auth;
class UserConfirmPass extends Component
{
    public $user, $current_password, $password, $userPassword, $confirm_password;

    public function changePassword()
    {       
        $this->user = Auth::user();
    
        $this->userPassword = $this->user->password;
        
        $this->validate([
            'current_password' => 'required',
            'password' => 'required|same:confirm_password|min:6',
            'confirm_password' => 'required',
        ]);

        if (!Hash::check($this->current_password, $this->userPassword)) {
            return back()->withErrors(['current_password'=>'password not match']);
        }

        $this->user->password = Hash::make($this->password);

        $this->user->update([
            'password' => $this->user->password
        ]);

        session()->flash('message', 'User Profile data Updated Successfully.');

        return redirect()->back()->with('success','password successfully updated');
    }


    public function render()
    {
        return view('livewire.user-confirm-pass');
    }
}
