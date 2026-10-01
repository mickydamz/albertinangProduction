<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\User;
use Carbon\Carbon;

class UserAvatar extends Component
{
    use WithFileUploads;

    public $fileName; // File input for avatar

    public function submit()
    {
        $this->validate([
            'fileName' => 'required|image|mimes:jpg,jpeg,png,svg,gif|max:2048',
        ]);
    
        // Use a more URL-friendly timestamp format
        $now = Carbon::now()->format('Y-m-d_H-i-s');
        $fileTitle = auth()->user()->email . '_' . $now;
        $extension = $this->fileName->getClientOriginalExtension();
        $fileName = $fileTitle . '.' . $extension;
    
        // Store the file
        $this->fileName->storeAs('public/todos', $fileName);
    
        // Update user avatar path (store without 'public/' prefix)
        auth()->user()->update(['avatar' => 'todos/' . $fileName]);
    
        session()->flash('successmessage', 'Profile updated successfully.');
        return redirect('/page-account-settings-account');
    }
    
    public function render()
    {
        return view('livewire.user-avatar');
    }
}
