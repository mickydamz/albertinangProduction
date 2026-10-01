<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class UserTransactionController extends Controller
{
    public function index()
    {
        // Get the logged-in user's transactions
        $transactions = Transaction::where('user_id', auth()->id())->with('products')->latest()->get();

        return view('user.transactions.index', compact('transactions'));
    }

     // Optionally, you can return data as JSON for dynamic front-end fetching
     public function fetchTransactions()
     {
         return Transaction::select(['created_at', 'total_amount'])->get();
     }
}
