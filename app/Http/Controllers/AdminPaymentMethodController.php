<?php


namespace App\Http\Controllers;

use App\Models\PaymentMethod;
use Illuminate\Http\Request;

class AdminPaymentMethodController extends Controller
{
    public function index()
    {
        $paymentMethods = PaymentMethod::all();
        return view('admin.payment-methods.index', compact('paymentMethods'));
    }

    public function create()
    {
        return view('admin.payment-methods.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'details' => 'required|string',
        ]);

        PaymentMethod::create($request->only('name', 'details'));

        return redirect()->route('admin.payment-methods.index')->with('success', 'Payment method created successfully.');
    }

    public function edit(PaymentMethod $paymentMethod)
    {
        return view('admin.payment-methods.edit', compact('paymentMethod'));
    }

    public function update(Request $request, PaymentMethod $paymentMethod)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'details' => 'required|string',
        ]);

        $paymentMethod->update($request->only('name', 'details'));

        return redirect()->route('admin.payment-methods.index')->with('success', 'Payment method updated successfully.');
    }

    public function destroy(PaymentMethod $paymentMethod)
    {
        $paymentMethod->delete();
        return redirect()->route('admin.payment-methods.index')->with('success', 'Payment method deleted successfully.');
    }
}
