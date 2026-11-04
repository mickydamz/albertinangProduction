<?php

namespace App\Http\Controllers;

use App\Models\CryptoAddress;
use Illuminate\Http\Request;

class CryptoAddressController extends Controller
{
    // Display a list of all crypto addresses
    public function index()
    {
        $cryptoAddresses = CryptoAddress::all();
        return view('admin.crypto-addresses.index', compact('cryptoAddresses'));
    }

    // Show the form to create a new crypto address
    public function create()
    {
        return view('admin.crypto-addresses.create');
    }

    // Store a newly created crypto address
    public function store(Request $request)
    {
        $request->validate([
            'currency' => 'required|string|max:255',
            // 'amount' => 'required|numeric',
            'address' => 'required|string|max:255',
        ]);

        CryptoAddress::create($request->only('currency', 'address'));

        return redirect()->route('admin.crypto-addresses.index')->with('success', 'Crypto address created successfully.');
    }

    // Show the form to edit an existing crypto address
    public function edit(CryptoAddress $cryptoAddress)
    {
        return view('admin.crypto-addresses.edit', compact('cryptoAddress'));
    }

    // Update the specified crypto address
    public function update(Request $request, CryptoAddress $cryptoAddress)
    {
        $request->validate([
            'currency' => 'required|string|max:255',
            // 'amount' => 'required|numeric',
            'address' => 'required|string|max:255',
        ]);

        $cryptoAddress->update($request->only('currency','address'));

        return redirect()->route('admin.crypto-addresses.index')->with('success', 'Crypto address updated successfully.');
    }

    // Delete the specified crypto address
    public function destroy(CryptoAddress $cryptoAddress)
    {
        $cryptoAddress->delete();
        return redirect()->route('admin.crypto-addresses.index')->with('success', 'Crypto address deleted successfully.');
    }
}
