<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminSupplierController extends Controller
{
    // Display a list of suppliers
    public function index()
    {
        $suppliers = User::where('role', 'supplier')->get();
        return view('admin.suppliers.index', compact('suppliers'));
    }

    // Show the form for creating a new supplier
    public function create()
    {
        return view('admin.suppliers.create');
    }

    // Store a new supplier
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $supplier = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'supplier',
            // Add any other fields necessary
        ]);

        return redirect('/admin/suppliers')->with('success', 'Supplier created successfully.');
    }

    // Show the form for editing a supplier's status
    public function edit(User $supplier)
    {
        return view('admin.suppliers.edit', compact('supplier'));
    }

    // Update the supplier's status
    public function update(Request $request, User $supplier)
    {
        $request->validate([
            'status' => 'required|in:green,yellow,banned',
        ]);

        $supplier->status = $request->status;
        $supplier->account_balance = $request->account_balance;
        $supplier->save();

        return redirect('/admin/suppliers')->with('success', 'Supplier status updated successfully.');
    }

    // Make a user a supplier
    public function makeSupplier(Request $request, User $user)
    {
        $user->role = 'supplier';
        $user->save();

        return redirect('/admin/suppliers')->with('success', 'User is now a supplier.');
    }

    public function verify($id)
{
    $supplier = Supplier::findOrFail($id);
    $supplier->status = 'verified'; // or whatever logic you use for statuses
    $supplier->save();

    return redirect()->route('admin.suppliers.index')->with('success', 'Supplier verified successfully.');
}

public function destroy($supplierId)
{
    // Find the supplier by its ID
    $supplier = User::findOrFail($supplierId);

    // Delete the supplier
    $supplier->delete();

    // Redirect back with a success message
    return redirect()->route('admin.suppliers.index')->with('success', 'Supplier deleted successfully.');
}


}
