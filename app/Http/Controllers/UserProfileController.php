<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

class UserProfileController extends Controller
{
    public function edit()
    {
        return view('user.profile.edit', ['user' => Auth::user()]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'shipping_address' => 'required|string|max:255',
            'country' => 'required|string|max:255',
        ]);

        $user = Auth::user();
        $user->name = $request->name;
        $user->shipping_address = $request->shipping_address;
        $user->country = $request->country;
        $user->save();

        return redirect()->route('user.dashboard')->with('success', 'Profile updated successfully.');
    }
}
