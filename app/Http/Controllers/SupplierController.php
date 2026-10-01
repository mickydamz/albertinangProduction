<?php

namespace App\Http\Controllers;
use App\Models\User; // Assuming your suppliers are stored in the User model
use App\Models\Product; // Make sure to import your Product model
use App\Models\Category; 
use Illuminate\Http\Request;



class SupplierController extends Controller
{
    // Display a list of all suppliers
    public function index(Request $request)
    {
        // $suppliers = User::where('role', 'supplier')->get(); // Adjust based on your roles
        // return view('suppliers.index', compact('suppliers')); // Create a view named suppliers.index

        $transactions = auth()->user()->transactions;
        $accountBalance = auth()->user()->account_balance;
    
        // Initial query to fetch all products
        $query = Product::query();
    
        // Apply sorting if provided
        if ($request->has('sort')) {
            switch ($request->input('sort')) {
                case 'price_low_high':
                    $query->orderBy('price', 'asc');
                    break;
                case 'price_high_low':
                    $query->orderBy('price', 'desc');
                    break;
                case 'rating':
                    $query->orderBy('price', 'desc');
                    break;
                default:
                    $query->orderBy('price', 'desc');
            }
        }
    
        // Paginate results
        $products = $query->paginate(20);

         // Display a list of all suppliers
 
        $suppliers = User::where('role', 'supplier')->get(); // Adjust based on your roles
        $categories = Category::all(); // Adjust based on your roles
        return view('suppliers.index', compact('suppliers','categories','products')); // Create a view named suppliers.index
    }
}

