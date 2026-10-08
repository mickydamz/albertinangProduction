// app/Listeners/MergeGuestCart.php
<?php

namespace App\Listeners;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Auth;

class MergeGuestCart
{
    public function handle(Login $event)
    {
        $sessionCart = session()->get('cart', []);

        foreach ($sessionCart as $item) {
            $product = Product::find($item['product_id']);
            if ($product && (int) $item['quantity'] > 0) {
                Cart::updateOrCreate(
                    [
                        'user_id' => Auth::id(),
                        'product_id' => $item['product_id'],
                    ],
                    [
                        'quantity' => \DB::raw("quantity + {$item['quantity']}"),
                    ]
                );
            }
        }

        // Clear the session cart
        session()->forget('cart');
    }
}