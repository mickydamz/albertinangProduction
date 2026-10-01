<?php


namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class SupplierProfileController extends Controller
{
    // Show the supplier's profile, products, and reviews
    public function show(User $supplier)
    {
        // Load products and reviews related to the supplier
       // Load products and reviews related to the supplier
$products = $supplier->products ?? collect(); // Return an empty collection if null
// $reviews = $supplier->reviews ?? collect(); // Return an empty collection if null

return view('supplier.profile', compact('supplier', 'products'));
    }
}
