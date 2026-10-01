<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\User;
use Hash;

class NormalUserUpdate extends Component
{


   // add  shipping_address,Postal code City Country



  public $name, $email, $data_id, $last_name, $daily,$weekly, $monthly, $yearly,$ghs,$dollar,$password,$check_password,$phone_no; 

  public $shipping_address, $postal_code, $city, $country; // New fields

  public function mount()
  {
      $data = User::findOrFail(auth()->user()->id);
      $this->data_id = auth()->user()->id;
      $this->name = $data->name;
      $this->email = $data->email;
      $this->phone_no = $data->phone_no;
      $this->shipping_address = $data->shipping_address;
      $this->postal_code = $data->postal_code;
      $this->city = $data->city;
      $this->country = $data->country;
  }

  public function resetInputFields()
  {
      $this->name = '';
      $this->email = '';
      $this->phone_no = '';
      $this->shipping_address = '';
      $this->postal_code = '';
      $this->city = '';
      $this->country = '';
  }

  public function update()
  {
      $this->validate([
          'name'       => 'required',
          'email'      => 'required|email',
          'phone_no'   => 'required',
          'shipping_address'    => 'required',
          'postal_code'=> 'required',
          'city'       => 'required',
          'country'    => 'required',
          'password'   => 'nullable|min:6',
      ]);

      $data = User::find($this->data_id);

      if ($data) {
          $data->update([
              'name'        => $this->name,
              'email'       => $this->email,
              'phone_no'    => $this->phone_no,
              'shipping_address'     => $this->shipping_address,
              'postal_code' => $this->postal_code,
              'city'        => $this->city,
              'country'     => $this->country,
              'password'    => $this->password ? Hash::make($this->password) : $data->password,
          ]);

          session()->flash('message', 'User Profile data Updated Successfully.');
      } else {
          session()->flash('failure', 'User not found.');
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
