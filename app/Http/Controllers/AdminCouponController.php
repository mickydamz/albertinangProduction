<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AdminCouponController extends Controller
{
   public function index(Request $request): View
{
    $query = Coupon::withCount('usages')->latest();

    if ($request->filled('search')) {
        $query->where('code', 'like', '%' . strtoupper($request->search) . '%');
    }

    if ($request->filter === 'active') {
        $query->where('is_active', true)
              ->where(fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()));
    } elseif ($request->filter === 'inactive') {
        $query->where('is_active', false);
    } elseif ($request->filter === 'expired') {
        $query->where('expires_at', '<', now());
    }

    $coupons      = $query->paginate(20);
    $activeCount  = Coupon::where('is_active', true)
                        ->where(fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()))
                        ->count();
    $expiredCount = Coupon::whereNotNull('expires_at')->where('expires_at', '<', now())->count();
    $totalUses    = Coupon::sum('used_count');

    return view('admin.coupons.index', compact('coupons', 'activeCount', 'expiredCount', 'totalUses'));
}

    public function create(): View
    {
        return view('admin.coupons.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'code'                => 'required|string|max:64|unique:coupons,code',
            'discount_type'       => 'required|in:percent,fixed',
            'value'               => 'required|numeric|min:0.01',
            'max_discount_amount' => 'nullable|numeric|min:0',
            'min_order_amount'    => 'nullable|numeric|min:0',
            'max_uses'            => 'nullable|integer|min:1',
            'multi_use'           => 'boolean',
            'is_active'           => 'boolean',
            'expires_at'          => 'nullable|date|after:now',
        ]);

        // Percent value must be 1–100
        if ($data['discount_type'] === 'percent' && $data['value'] > 100) {
            return back()->withErrors(['value' => 'Percentage discount cannot exceed 100.'])->withInput();
        }

        $data['code']             = strtoupper(trim($data['code']));
        $data['min_order_amount'] = $data['min_order_amount'] ?? 0;
        $data['multi_use']        = $request->boolean('multi_use');
        $data['is_active']        = $request->boolean('is_active', true);

        Coupon::create($data);

        return redirect()->route('admin.coupons.index')
            ->with('success', 'Coupon ' . $data['code'] . ' created successfully.');
    }

    public function edit(Coupon $coupon): View
    {
        $coupon->loadCount('usages');

        return view('admin.coupons.edit', compact('coupon'));
    }

    public function update(Request $request, Coupon $coupon): RedirectResponse
    {
        $data = $request->validate([
            'code'                => 'required|string|max:64|unique:coupons,code,' . $coupon->id,
            'discount_type'       => 'required|in:percent,fixed',
            'value'               => 'required|numeric|min:0.01',
            'max_discount_amount' => 'nullable|numeric|min:0',
            'min_order_amount'    => 'nullable|numeric|min:0',
            'max_uses'            => 'nullable|integer|min:1',
            'multi_use'           => 'boolean',
            'is_active'           => 'boolean',
            'expires_at'          => 'nullable|date',
        ]);

        if ($data['discount_type'] === 'percent' && $data['value'] > 100) {
            return back()->withErrors(['value' => 'Percentage discount cannot exceed 100.'])->withInput();
        }

        $data['code']             = strtoupper(trim($data['code']));
        $data['min_order_amount'] = $data['min_order_amount'] ?? 0;
        $data['multi_use']        = $request->boolean('multi_use');
        $data['is_active']        = $request->boolean('is_active');

        $coupon->update($data);

        return redirect()->route('admin.coupons.index')
            ->with('success', 'Coupon ' . $coupon->code . ' updated successfully.');
    }

    public function destroy(Coupon $coupon): RedirectResponse
    {
        // Prevent deleting a coupon that has been used on orders
        if ($coupon->orders()->exists()) {
            return back()->with('error', 'Cannot delete a coupon that has been applied to orders. Deactivate it instead.');
        }

        $code = $coupon->code;
        $coupon->delete();

        return redirect()->route('admin.coupons.index')
            ->with('success', 'Coupon ' . $code . ' deleted.');
    }

    public function toggleActive(Coupon $coupon): RedirectResponse
    {
        $coupon->update(['is_active' => !$coupon->is_active]);

        $state = $coupon->is_active ? 'activated' : 'deactivated';

        return back()->with('success', 'Coupon ' . $coupon->code . ' ' . $state . '.');
    }
}