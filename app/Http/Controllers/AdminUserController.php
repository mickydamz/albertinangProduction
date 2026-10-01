<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AdminUserController extends Controller
{
    // Display a list of users with search + filters
    public function index(Request $request)
    {
        $search   = $request->get('search');
        $role     = $request->get('role');
        $status   = $request->get('status');
        $verified = $request->get('verified');
        $dateFrom = $request->get('date_from');
        $dateTo   = $request->get('date_to');

        $users = User::with('referredBy')
            // Search
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name',     'like', '%' . $search . '%')
                      ->orWhere('email',    'like', '%' . $search . '%')
                      ->orWhere('city',     'like', '%' . $search . '%')
                      ->orWhere('country',  'like', '%' . $search . '%')
                      ->orWhere('phone_no', 'like', '%' . $search . '%');
                });
            })
            // Role filter
            ->when($role, fn($q) => $q->where('role', $role))
            // Status filter
            ->when($status, fn($q) => $q->where('status', $status))
            // Verified filter (accepts "1" or "0" from the form)
            ->when($verified !== null && $verified !== '', fn($q) => $q->where('verified', (bool) $verified))
            // Date range
            ->when($dateFrom, fn($q) => $q->whereDate('created_at', '>=', $dateFrom))
            ->when($dateTo,   fn($q) => $q->whereDate('created_at', '<=', $dateTo))
            ->latest()
            ->paginate(10)
            ->withQueryString(); // keeps all filter params in pagination links

        return view('admin.users.index', compact(
            'users', 'search', 'role', 'status', 'verified', 'dateFrom', 'dateTo'
        ));
    }

    // Show the form for creating a new user
    public function create()
    {
        return view('admin.users.create');
    }

    // Store a new user
    public function store(Request $request)
    {
        $request->validate([
            'name'             => 'required|string|max:255',
            'email'            => 'required|string|email|max:255|unique:users',
            'password'         => 'required|string|min:8|confirmed',
            'role'             => 'required|in:supplier,admin,user,affiliate,manager',
            'status'           => 'required|in:green,yellow,banned',
            'verified'         => 'required|boolean',
            'is_hidden'        => 'nullable|boolean',
            'description'      => 'nullable|string',
            'avatar'           => 'nullable|image|mimes:jpg,jpeg,png,bmp,gif,svg|max:2048',
            'city'             => 'nullable|string|max:255',
            'postal_code'      => 'nullable|string|max:255',
            'phone_no'         => 'nullable|string|max:20',
            'country'          => 'nullable|string|max:255',
            'shipping_address' => 'nullable|string',
        ]);

        $avatarPath = null;
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
        }

        User::create([
            'name'             => $request->name,
            'email'            => $request->email,
            'password'         => Hash::make($request->password),
            'role'             => $request->role,
            'status'           => $request->status,
            'verified'         => $request->verified,
            'is_hidden'        => $request->boolean('is_hidden'),
            'description'      => $request->description,
            'fileName'         => $avatarPath,
            'city'             => $request->city,
            'postal_code'      => $request->postal_code,
            'phone_no'         => $request->phone_no,
            'country'          => $request->country,
            'shipping_address' => $request->shipping_address,
        ]);

        return redirect('/admin/users')->with('success', 'User created successfully.');
    }

    // Show the form for editing a user
    // Show a single user's details
    public function show(User $user)
    {
        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    // Update an existing user
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'             => 'required|string|max:255',
            'email'            => 'required|email|max:255|unique:users,email,' . $user->id,
            'account_balance'  => 'nullable|numeric|min:0',
            'role'             => 'required|in:supplier,admin,user,affiliate,manager',
            'status'           => 'required|in:green,yellow,banned',
            'verified'         => 'required|boolean',
            'is_hidden'        => 'nullable|boolean',
            'description'      => 'nullable|string',
            'avatar'           => 'nullable|image|mimes:jpg,jpeg,png,bmp,gif,svg|max:2048',
            'city'             => 'nullable|string|max:255',
            'postal_code'      => 'nullable|string|max:255',
            'phone_no'         => 'nullable|string|max:20',
            'country'          => 'nullable|string|max:255',
            'shipping_address' => 'nullable|string',
        ]);

        $user->name             = $request->name;
        $user->email            = $request->email;
        $user->account_balance  = $request->account_balance;
        $user->role             = $request->role;
        $user->status           = $request->status;
        $user->verified         = $request->verified;
        $user->is_hidden        = $request->boolean('is_hidden');
        $user->description      = $request->description;
        $user->city             = $request->city;
        $user->postal_code      = $request->postal_code;
        $user->phone_no         = $request->phone_no;
        $user->country          = $request->country;
        $user->shipping_address = $request->shipping_address;

        if ($request->hasFile('avatar')) {
            if ($user->fileName) {
                Storage::disk('public')->delete($user->fileName);
            }
            $user->fileName = $request->file('avatar')->store('avatars', 'public');
        }

        $user->save();

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    // Delete a user
    public function destroy(User $user)
    {
        $user->sentMessages()->delete();
        $user->receivedMessages()->delete();
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }
}