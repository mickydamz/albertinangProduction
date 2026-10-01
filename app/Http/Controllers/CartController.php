<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;

use App\Models\Category;
use App\Models\PaymentMethod;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaction;
use App\Models\User;
use App\Mail\TransactionMail;
use Illuminate\Support\Facades\Mail;

class CartController extends Controller
{
    
public function index()
{
    // Initialize variables
    $cartItems = collect(); // Empty collection for non-logged-in users
    $paymentMethods = PaymentMethod::where('is_active', 1)->get();
    $countries = Country::all();
    $categories = Category::with('subcategories')->get();
    $topLevelCategories = $categories; // Reuse categories to avoid redundant query
    
    // Fetch cart items only for logged-in users
    // if (auth()->check()) {
    //     $cartItems = Cart::where('user_id', auth()->id())->get();
    //     return view('user.cart.index', compact('cartItems', 'paymentMethods', 'countries', 'bankAccounts', 'topLevelCategories', 'categories'));
    // }
    
    // For non-logged-in users, return guest cart view
    return view('cart.index', compact('topLevelCategories', 'categories'));
}


// Example route: /cart/increase/{cartItem}
public function increaseQuantity($cartItemId)
{
    $cartItem = Cart::where('id', $cartItemId)
                    ->where('user_id', auth()->id())
                    ->firstOrFail();



    
  

    // Check if there is enough stock
    $product = $cartItem->product;
    if ($product->stock < $cartItem->quantity + 1) {
        return redirect()->route('cart.index')->withErrors(['error' => "Insufficient stock for product: {$product->name}."]);
    }

    // Increase the quantity by 1
    $cartItem->quantity += 1;
    $cartItem->save();

    return redirect()->route('cart.index')->with('success', 'Product quantity increased.');
}


public function decreaseQuantity($cartItemId)
{
    $cartItem = Cart::where('id', $cartItemId)
                    ->where('user_id', auth()->id())
                    ->firstOrFail();



    
  

    // Check if there is enough stock
    $product = $cartItem->product;
    if ($product->stock < $cartItem->quantity - 1) {
        return redirect()->route('cart.index')->withErrors(['error' => "Insufficient stock for product: {$product->name}."]);
    }

    // Increase the quantity by 1
    $cartItem->quantity -= 1;
    $cartItem->save();

    return redirect()->route('cart.index')->with('success', 'Product quantity increased.');
}



public function remove($cartItemId)
{
    $cartItem = Cart::where('id', $cartItemId)
                    ->where('user_id', auth()->id())
                    ->firstOrFail();



    // Check if there is enough stock
    $product = $cartItem->product;
    if ($product->stock < $cartItem->quantity + 1) {
        return redirect()->route('cart.index')->withErrors(['error' => "Insufficient stock for product: {$product->name}."]);
    }

    // Increase the quantity by 1
    $cartItem->delete();
    

    return redirect()->route('cart.index')->with('success', 'Product quantity increased.');
}



    public function add(Request $request, Product $product)
    {
        if ($product->stock <= 0) {
            return redirect()->back()->withErrors(['error' => 'Product is out of stock.']);
        }

        $cartItem = Cart::where('user_id', auth()->id())
            ->where('product_id', $product->id)
            ->first();

        if ($cartItem) {
            $cartItem->quantity += 1;
            $cartItem->save();
        } else {
            Cart::create([
                'user_id' => auth()->user()->id,
                'product_id' => $product->id,
                'quantity' => 1,
            ]);
        }
        $cartUrl = route('cart.index'); // Adjust this route as necessary
        session()->flash('added_to_cart', "Product added to cart successfully. <a href='{$cartUrl}' class='alert-link'>Proceed to Cart</a>");
        return redirect()->back()->with('success', 'Product added to cart successfully.');
    }

    public function checkout(Request $request)
{
    // Define the logged-in user
    $user = Auth::user();
    
    if ($request->isMethod('get')) {
        $paymentMethods = PaymentMethod::where('is_active', true)->get();
        $countries = Country::all();

        // Recall the delivery address from the customer's most recent order.
        // If they have never ordered, this stays null and the field is left blank
        // (we intentionally do NOT fall back to the profile address).
        $lastOrderAddress = $user
            ? optional(
                $user->orders()
                    ->whereNotNull('shipping_address')
                    ->where('shipping_address', '!=', '')
                    ->latest()
                    ->first()
              )->shipping_address
            : null;

        return view('checkout', compact('paymentMethods', 'countries', 'lastOrderAddress'));
    }

    // Validate request
    $request->validate([
        'payment_option' => 'required|in:full,half,escrow',
    ]);

    $cartItems = Cart::where('user_id', $user->id)->get();

    // Check if cart is empty
    if ($cartItems->isEmpty()) {
        return redirect()->route('cart.index')->withErrors(['error' => 'Your cart is empty.']);
    }

    $totalAmount = 0;

    // Calculate total amount and check stock
    foreach ($cartItems as $item) {
        $product = $item->product;

        if ($product->stock < $item->quantity) {
            return redirect()->route('cart.index')->withErrors([
                'error' => "Insufficient stock for product: {$product->name}. Only {$product->stock} left.",
            ]);
        }

        // Calculate total
        $totalAmount += $product->price * $item->quantity;
    }

    // Determine payment amount
    $paymentAmount = $totalAmount; // Full amount for all payment options
    
    // Handle balance check for "full" and "half" options
    if ($request->payment_option !== 'escrow') {
        // If "half" payment is selected, check if the user has enough balance for half the total amount
        if ($request->payment_option === 'half') {
            $paymentAmount = $totalAmount / 2; // Deduct half the total amount
        }

        // For "half" and "full" payment options, check if the user has enough balance
        if ($user->account_balance < $paymentAmount) {
            return redirect()->back()->withErrors(['error' => 'Insufficient account balance for this transaction.']);
        }

        // Deduct the amount (half for "half" option or full for "full" option)
        $user->account_balance -= $paymentAmount;
        $user->save();

        // Add the deducted amount to the supplier's account balance
        foreach ($cartItems as $item) {
            $product = $item->product;
            $supplier = $product->supplier;

            // If payment is full or half, add the respective amount to the supplier's account balance
            if ($request->payment_option === 'full') {
                $supplier->account_balance += $product->price * $item->quantity;
            } elseif ($request->payment_option === 'half') {
                $supplier->account_balance += ($product->price * $item->quantity) / 2;
            }
            // dd($supplier->name);
            $supplier->save();
        }
    }

    // Create transaction
    $escrowAmount = 0; // Default to 0
    if ($request->payment_option === 'half') {
        $escrowAmount = $totalAmount / 2; // Half amount held in escrow
    } elseif ($request->payment_option === 'escrow') {
        $escrowAmount = $totalAmount; // Full amount held in escrow
    }

    // Assign payment_method_id based on the payment option
    $paymentMethodId = 1; // Default to full payment (payment_method_id = 1)
    if ($request->payment_option === 'half') {
        $paymentMethodId = 2; // Half payment (payment_method_id = 2)
    } elseif ($request->payment_option === 'escrow') {
        $paymentMethodId = 3; // Escrow payment (payment_method_id = 3)
    }

    $transaction = \App\Models\Transaction::create([
        'user_id' => $user->id,
        'payment_method_id' => $paymentMethodId, // Use the updated payment method ID
        'total_amount' => $totalAmount,
        'escrow_amount' => $escrowAmount,
        'status' => ($request->payment_option === 'full') ? 1 : 0, // Payment complete for full, pending for others
    ]);

    // Reduce stock and attach products to transaction
    foreach ($cartItems as $item) {
        $product = $item->product;
        $product->stock -= $item->quantity;
        $product->save();

        $transaction->products()->attach($product->id, [
            'quantity' => $item->quantity,
            'price' => $product->price,
        ]);
    }

    // Clear cart
    Cart::where('user_id', $user->id)->delete();
    //  $this->sendTransactionEmail();

     $user = User::find($transaction->user_id);
$paymentMethod = PaymentMethod::find($transaction->payment_method_id)->name; // Assuming 'name' is the field for the payment method name
$amount = $transaction->total_amount;

Mail::to($user->email)->send(new TransactionMail($user, $paymentMethod, $amount));

    return redirect()->route('user.transactions')->with('success', 'Checkout completed successfully.');
}



public function sendTransactionEmail($transactionId)
{
    $transaction = Transaction::find($transactionId);
    $user = User::find($transaction->user_id);
    $paymentMethod = PaymentMethod::find($transaction->payment_method_id)->name; // Assuming 'name' is the field for the payment method name
    $amount = $transaction->total_amount;

    Mail::to($user->email)->send(new TransactionMail($user, $paymentMethod, $amount));

    if (Mail::failures()) {
        return response()->json(['message' => 'Email sending failed'], 500);
    }

    return response()->json(['message' => 'Email sent successfully'], 200);
}

    
}

