<?php

namespace App\Http\Controllers;

use App\Models\User; // Assuming your User model represents distributors as well
use Illuminate\Http\Request;

class DistributorController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'supplier');
    
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('location', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
        }
    
        $distributors = $query->paginate(10);
    
        return view('supplier.distributors.index', compact('distributors'));
    }
    


public function show($id)
{
    // Find the distributor by ID
    $distributor = User::where('role', 'supplier')->findOrFail($id); // This will throw a 404 if not found

    // Pass the distributor data to the view
    return view('supplier.distributors.show', compact('distributor'));
}


}
