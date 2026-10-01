<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\User;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;

class AdminTransactionController extends Controller
{
    // Display a listing of all transactions
    public function index()
    {
        $transactions = Transaction::with(['user', 'paymentMethod', 'products'])->get();
        return view('admin.transactions.index', compact('transactions'));
    }

    // Show the details of a specific transaction
    public function show(Transaction $transaction)
    {
        // Load additional relationships if necessary
        $transaction->load(['user', 'paymentMethod', 'products']);
        return view('admin.transactions.show', compact('transaction'));
    }

    // Show the form for creating a new transaction
    public function create()
    {
        $users = User::all(); // Get all users
        $paymentMethods = PaymentMethod::all(); // Get all payment methods
        return view('admin.transactions.create', compact('users', 'paymentMethods'));
    }

    // Store a newly created transaction in storage
   // Store a newly created transaction in storage
   public function store(Request $request)
   {
       // Validate the incoming request data
       $request->validate([
           'user_id' => 'required|exists:users,id', // Ensure the user exists
           'total_amount' => 'required|numeric|min:0', // Total amount must be a non-negative number
           'escrow_amount' => 'required|numeric|min:0', // Escrow amount must be a non-negative number
           'status' => 'required|in:0,1', // Status must be either Pending (0) or Complete (1)
       ]);
   
       // Logic to determine the payment method
       $paymentMethodId = 0; // Default value
       if ($request->escrow_amount > 0 && $request->escrow_amount < $request->total_amount) {
           $paymentMethodId = 3; // Escrow payment
       } elseif ($request->escrow_amount == 0 && $request->total_amount > 0) {
           $paymentMethodId = 1; // Full payment
       } elseif ($request->escrow_amount == $request->total_amount) {
           $paymentMethodId = 2; // Half payment (if that's how you want to define it, for example)
       }
   
       // Create the transaction with the appropriate payment method ID
       Transaction::create([
           'user_id' => $request->user_id,
           'total_amount' => $request->total_amount,
           'escrow_amount' => $request->escrow_amount,
           'status' => $request->status,
           'payment_method_id' => $paymentMethodId, // Assign the payment method ID
       ]);
   
       // Redirect back with a success message
       return redirect()->route('admin.transactions.index')->with('success', 'Transaction created successfully.');
   }


//    public function store(Request $request)
//     {
//         // Validate the incoming request data
//         $request->validate([
//             'user_id' => 'required|exists:users,id', // Ensure the user exists
//             'total_amount' => 'required|numeric|min:0', // Total amount must be a non-negative number
//             'escrow_amount' => 'required|numeric|min:0', // Escrow amount must be a non-negative number
//             'status' => 'required|in:0,1', // Status must be either Pending (0) or Complete (1)
//         ]);

//         // Logic to determine the payment method
//         $paymentMethodId = 0; // Default value
//         if ($request->escrow_amount > 0 && $request->escrow_amount < $request->total_amount) {
//             $paymentMethodId = 3; // Escrow payment
//         } elseif ($request->escrow_amount == 0 && $request->total_amount > 0) {
//             $paymentMethodId = 1; // Full payment
//         } elseif ($request->escrow_amount == $request->total_amount) {
//             $paymentMethodId = 2; // Half payment (if that's how you want to define it, for example)
//         }

//         // Create the transaction with the appropriate payment method ID
//         $transaction = Transaction::create([
//             'user_id' => $request->user_id,
//             'total_amount' => $request->total_amount,
//             'escrow_amount' => $request->escrow_amount,
//             'status' => $request->status,
//             'payment_method_id' => $paymentMethodId, // Assign the payment method ID
//         ]);

//         // If status is 1 (Complete) and the payment method is escrow, add escrow amount to suppliers' account balances
//         if ($transaction->status === 1 && $paymentMethodId === 3) {
//             // Get all products in the transaction
//             foreach ($transaction->products as $product) {
//                 $supplier = $product->supplier; // Get the supplier of the product

//                 // Calculate the proportional escrow amount based on the product's quantity in the transaction
//                 $totalQuantity = $transaction->products->sum(function ($prod) {
//                     return $prod->pivot->quantity; // Get the quantity from the pivot table
//                 });

//                 $productEscrowAmount = ($product->pivot->quantity / $totalQuantity) * $transaction->escrow_amount;

//                 // Add the proportional escrow amount to the supplier's account balance
//                 $supplier->account_balance += $productEscrowAmount;
//                 $supplier->save();
//             }
//         }

//         // Redirect back with a success message
//         return redirect()->route('admin.transactions.index')->with('success', 'Transaction created successfully.');
//     }

   

    // Show the form for editing a specific transaction
    public function edit(Transaction $transaction)
    {
        $users = User::all(); // Get all users
        $paymentMethods = PaymentMethod::all(); // Get all payment methods
        return view('admin.transactions.edit', compact('transaction', 'users', 'paymentMethods'));
    }

    // Update the specified transaction in storage
    // public function update(Request $request, Transaction $transaction)
    // {
    //     $request->validate([
    //         'user_id' => 'required|exists:users,id',
    //         'total_amount' => 'required|numeric|min:0',
    //         'escrow_amount' => 'required|numeric|min:0',
    //         'status' => 'required|in:0,1', // Assuming 0 is Pending and 1 is Complete
    //     ]);
    
    //     $transaction->update($request->all());
    //     return redirect()->route('admin.transactions.index')->with('success', 'Transaction updated successfully.');
    // }

     // Update the specified transaction in storage
        // Update the specified transaction in storage
    public function update(Request $request, Transaction $transaction)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'total_amount' => 'required|numeric|min:0',
            'escrow_amount' => 'required|numeric|min:0',
            'status' => 'required|in:0,1', // Assuming 0 is Pending and 1 is Complete
        ]);

        $transaction->update($request->all());
        // dd($request->status);
        // dd($transaction->payment_method_id);

        // If status is 1 (Complete), we need to handle the payment distribution
        if ($request->status === '1') {
            // dd($request->status);
            $user = User::find($transaction->user_id);

            // **Escrow Payment Method**
            if ($transaction->payment_method_id === '3') {
                // Distribute escrow amount to suppliers
                foreach ($transaction->products as $product) {
                    $supplier = $product->supplier; // Get the supplier of the product

                    // Calculate the proportional escrow amount based on the product's quantity in the transaction
                    $totalQuantity = $transaction->products->sum(function ($prod) {
                        return $prod->pivot->quantity; // Get the quantity from the pivot table
                    });

                    $productEscrowAmount = ($product->pivot->quantity / $totalQuantity) * $transaction->escrow_amount;

                    // Add the proportional escrow amount to the supplier's account balance
                    $supplier->account_balance += $productEscrowAmount;
                    // dd($supplier->name);
                    $supplier->save();
                }
            }

            // **Half Payment Method**
            if ($transaction->payment_method_id === '2') {
                // Distribute half of the total amount to suppliers
                foreach ($transaction->products as $product) {
                    $supplier = $product->supplier; // Get the supplier of the product

                    // Calculate the proportional half payment amount based on the product's quantity in the transaction
                    $totalQuantity = $transaction->products->sum(function ($prod) {
                        return $prod->pivot->quantity; // Get the quantity from the pivot table
                    });

                    $productHalfPaymentAmount = ($product->pivot->quantity / $totalQuantity) * ($transaction->total_amount / 2);

                    // Add the proportional half payment amount to the supplier's account balance
                    $supplier->account_balance += $productHalfPaymentAmount;
                    $supplier->save();
                }
            }

          
        }

        // Redirect back with a success message
        return redirect()->route('admin.transactions.index')->with('success', 'Transaction updated successfully.');
    }

    // Optionally, you can delete a transaction
    public function destroy(Transaction $transaction)
    {
        $transaction->delete();
        return redirect()->route('admin.transactions.index')->with('success', 'Transaction deleted successfully.');
    }
}
