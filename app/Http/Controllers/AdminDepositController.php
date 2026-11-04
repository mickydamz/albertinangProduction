<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Deposit;

class AdminDepositController extends Controller
{
    public function index()
    {
        // Fetch deposits with user and payment method relationships, ordered by the latest
        $deposits = Deposit::with(['user', 'paymentMethod'])
                            ->latest() // Orders the results by the latest
                            ->paginate(10); // Paginate the results

        return view('admin.deposit.index', compact('deposits'));
    }


    public function approve($id)
    {
        $deposit = Deposit::findOrFail($id);
        $deposit->update(['status' => 1]); // Update to approved status (1)

        return redirect()->route('admin.deposit.index')->with('success', 'Deposit approved successfully.');
    }

    public function reject($id)
    {
        $deposit = Deposit::findOrFail($id);
        $deposit->update(['status' => 2]); // Update to rejected status (2)

        return redirect()->route('admin.deposit.index')->with('success', 'Deposit rejected successfully.');
    }
}
