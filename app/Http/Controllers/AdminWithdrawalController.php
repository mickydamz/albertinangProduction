<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Withdrawal;

class AdminWithdrawalController extends Controller
{
    public function index()
    {
        // Fetch withdrawals with user and payment method relationships
        $withdrawals = Withdrawal::with(['user', 'paymentMethod'])->latest()->get();

        return view('admin.withdraw.index', compact('withdrawals'));
    }

    public function approve($id)
    {
        // Find the withdrawal by ID and update the status to approved (1)
        $withdrawal = Withdrawal::findOrFail($id);
        $withdrawal->update(['status' => 1]); // Status 1 means approved

        return redirect()->route('admin.withdraw.index')->with('success', 'Withdrawal approved successfully.');
    }

    public function reject($id)
    {
        // Find the withdrawal by ID and update the status to rejected (2)
        $withdrawal = Withdrawal::findOrFail($id);
        $withdrawal->update(['status' => 2]); // Status 2 means rejected

        return redirect()->route('admin.withdrawal.index')->with('success', 'Withdrawal rejected successfully.');
    }

    public function destroy($id)
    {
        // Find the withdrawal by ID and delete it
        $withdrawal = Withdrawal::findOrFail($id);
        $withdrawal->delete();

        return redirect()->route('admin.withdrawal.index')->with('success', 'Withdrawal deleted successfully.');
    }
}
