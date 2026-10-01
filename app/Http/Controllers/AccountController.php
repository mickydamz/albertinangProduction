<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class AccountController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
{
    $user       = Auth::user();
    $orderCount = $user->orders()->count();

    return view('sims.account', compact('user', 'orderCount'));
}

    public function update(Request $request)
{
    // Fetch a fresh Eloquent instance — fixes the most common cause
    $user = \App\Models\User::find(Auth::id());

    $validated = $request->validate([
        'name'             => 'required|string|max:255',
        'email'            => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
        'phone_no'         => 'nullable|string|max:20',
        'date_of_birth'    => 'nullable|date|before:today',
        'city'             => 'nullable|string|max:100',
        'postal_code'      => 'nullable|string|max:20',
        'shipping_address' => 'nullable|string|max:500',
        'state'         => 'nullable|string|max:255',
    ]);

    try {
        $user->update($validated);
        return redirect()->route('account.index')->with('success', 'Profile updated successfully.');
    } catch (\Exception $e) {
        Log::error('Profile update failed for user ' . $user->id . ': ' . $e->getMessage());
        return redirect()->back()->withInput()->with('error', $e->getMessage()); // show real error for now
    }
}

    public function changePasswordForm()
{
    return view('sims.change-password');
}

public function changePassword(Request $request)
{
    $user = Auth::user();

    $request->validate([
        'current_password'      => 'required|string',
        'password'              => 'required|string|min:8|confirmed',
        'password_confirmation' => 'required|string',
    ]);

    if (!Hash::check($request->current_password, $user->password)) {
        return redirect()->back()
            ->withInput()
            ->withErrors(['current_password' => 'Your current password is incorrect.']);
    }

    try {
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('account.index')
            ->with('success', 'Password changed successfully.');
    } catch (\Exception $e) {
        Log::error('Password change failed for user ' . $user->id . ': ' . $e->getMessage());
        return redirect()->back()
            ->with('error', 'Failed to change password. Please try again.');
    }
}


}