<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupplierTransactionController extends Controller
{
    /**
     * Display a listing of the transactions for the supplier's products.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $supplierId = Auth::id(); // Assuming the supplier is authenticated

        // Get transactions involving the supplier's products
        $transactions = Transaction::whereHas('products', function ($query) use ($supplierId) {
            $query->where('supplier_id', $supplierId);
        })->with(['products' => function ($query) use ($supplierId) {
            $query->where('supplier_id', $supplierId);
        }, 'user'])->get();

        return view('supplier.transactions.index', compact('transactions'));
    }
}
