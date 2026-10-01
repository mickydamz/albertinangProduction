<?php

namespace App\Http\Controllers;

use App\Models\PaystackTransaction;
use Illuminate\Http\Request;

class AdminPaystackTransactionController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    public function index(Request $request)
    {
        $query = PaystackTransaction::with('order')->orderByDesc('created_at');

        if ($request->filled('search')) {
            $q = trim($request->search);
            $query->where(function ($qb) use ($q) {
                $qb->where('reference',   'like', "%{$q}%")
                   ->orWhere('paystack_id','like', "%{$q}%")
                   ->orWhere('status',     'like', "%{$q}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('linked')) {
            if ($request->linked === '1') {
                $query->whereNotNull('order_id');
            } elseif ($request->linked === '0') {
                $query->whereNull('order_id');
            }
        }

        $transactions = $query->paginate(30)->withQueryString();
        $totalCount   = PaystackTransaction::count();
        $linkedCount  = PaystackTransaction::whereNotNull('order_id')->count();
        $orphanCount  = PaystackTransaction::whereNull('order_id')->count();

        return view('admin.paystack_transactions.index', compact(
            'transactions',
            'totalCount',
            'linkedCount',
            'orphanCount'
        ));
    }

    public function show(PaystackTransaction $paystackTransaction)
    {
        $paystackTransaction->load('order.items');
        return view('admin.paystack_transactions.show', compact('paystackTransaction'));
    }
}
