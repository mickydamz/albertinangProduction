<?php

namespace App\Http\Controllers;
use App\Models\BankAccount;
use App\Models\Country;
use Illuminate\Http\Request;

class AdminBankController extends Controller
{
    // List all bank accounts
    public function index()
    {
        $bankAccounts = BankAccount::with('country')->latest()->get();
        return view('admin.bank.index', compact('bankAccounts'));
    }

    // Show the form to create a new bank account
    public function create()
    {
        $countries = Country::all(); // Get all countries to choose from
        return view('admin.bank.create', compact('countries'));
    }

    // Store a new bank account
    public function store(Request $request)
    {
        $request->validate([
            'country_id' => 'required|exists:countries,id',
            'bank_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:255',
        ]);

        BankAccount::create([
            'country_id' => $request->country_id,
            'bank_name' => $request->bank_name,
            'account_number' => $request->account_number,
        ]);

        return redirect()->route('admin.bank.index')->with('success', 'Bank account created successfully.');
    }

    // Show the form to edit an existing bank account
    public function edit($id)
    {
        $bankAccount = BankAccount::findOrFail($id);
        $countries = Country::all();
        return view('admin.bank.edit', compact('bankAccount', 'countries'));
    }

    // Update an existing bank account
    public function update(Request $request, $id)
    {
        $request->validate([
            'country_id' => 'required|exists:countries,id',
            'bank_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:255',
        ]);

        $bankAccount = BankAccount::findOrFail($id);
        $bankAccount->update([
            'country_id' => $request->country_id,
            'bank_name' => $request->bank_name,
            'account_number' => $request->account_number,
        ]);

        return redirect()->route('admin.bank.index')->with('success', 'Bank account updated successfully.');
    }

    // Delete a bank account
    public function destroy($id)
    {
        $bankAccount = BankAccount::findOrFail($id);
        $bankAccount->delete();

        return redirect()->route('admin.bank.index')->with('success', 'Bank account deleted successfully.');
    }
}
