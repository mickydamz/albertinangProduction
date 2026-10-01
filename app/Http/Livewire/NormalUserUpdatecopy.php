<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\User;
use Hash;

class NormalUserUpdateCopy extends Component
{


   // add  Address,Postal code City Country



  public $name, $email, $data_id, $last_name, $daily,$weekly, $monthly, $yearly,$ghs,$dollar,$password,$check_password,$phone_no; 

    public function mount()
    {
        $data = User::findOrFail(auth()->user()->id);
        $this->data_id = auth()->user()->id;
        $this->name = $data->name;
        $this->email = $data->email;
        $this->phone_no = $data->phone_no;
        
    }


    public function resetInputFields()
    {
    	$this->name = '';
    	$this->email = '';
    	
    }
    


    public function update()
    {
        
            $this->validate = $this->validate([
            'name'         =>    'required',
    		'email'		   =>	'required',
            ]);

            $data = User::find($this->data_id);

        
        if($data){
        
            $data->update([
            'name'       =>  $this->name,
            'email'      =>  $this->email,
            'phone_no'      =>  $this->phone_no,
            'password'      =>  Hash::make($this->password),
        
        ]);
        
         session()->flash('message', 'User Profile data Updated Successfully.');
            
        }else{
            
            session()->flash('failure', 'password confirmation does not match.');
            
        }
        
    
       

    }
    
    public function changePassword(Request $request)
    {       
        $user = Auth::user();
    
        $userPassword = $user->password;
        
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|same:confirm_password|min:6',
            'confirm_password' => 'required',
        ]);

        if (!Hash::check($request->current_password, $userPassword)) {
            return back()->withErrors(['current_password'=>'password not match']);
        }

        $user->password = Hash::make($request->password);

        $user->save();

        return redirect()->back()->with('success','password successfully updated');
    }
    

    public function render()
    {
        
        return view('livewire.normal-user-update');
    }
}
