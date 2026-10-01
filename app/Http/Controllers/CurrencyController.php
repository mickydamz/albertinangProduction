<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use Illuminate\Http\Request;

class CurrencyController extends Controller
{
    // ─────────────────────────────────────────────
    //  PUBLIC — used by the frontend JS
    // ─────────────────────────────────────────────

    /**
     * POST /currency/set
     * Saves the selected currency code to the session (called by JS after manual or auto-detect change).
     */
    public function setSession(Request $request)
    {
        $code = strtoupper(trim($request->input('code', '')));
        $allowed = Currency::active()->pluck('code')->toArray();
        if (in_array($code, $allowed)) {
            session(['currency' => $code]);
        }
        return response()->json(['ok' => true, 'currency' => session('currency', 'NGN')]);
    }

    /**
     * GET /currencies
     * Returns all active currencies as JSON for the frontend switcher.
     */
    public function publicIndex()
    {
        $currencies = Currency::active()->map(fn($c) => [
            'code'        => $c->code,
            'symbol'      => $c->symbol,
            'name'        => $c->name,
            'rate_to_ngn' => $c->rate_to_ngn,
            'is_base'     => $c->is_base,
        ]);

        return response()->json($currencies)
            ->header('Cache-Control', 'public, max-age=300');
    }

    // ─────────────────────────────────────────────
    //  ADMIN CRUD
    // ─────────────────────────────────────────────

    /** GET /admin/currencies */
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search', ''));

        $currencies = Currency::when($search !== '', fn ($q) => $q->where('code', 'like', "%{$search}%")
                                                                 ->orWhere('name', 'like', "%{$search}%")
                                                                 ->orWhere('symbol', 'like', "%{$search}%"))
            ->orderBy('sort_order')
            ->paginate(15)
            ->withQueryString();

        return view('admin.currencies.index', compact('currencies', 'search'));
    }

    /** GET /admin/currencies/create */
    public function create()
    {
        return view('admin.currencies.form', ['currency' => new Currency()]);
    }

    /** POST /admin/currencies */
    public function store(Request $request)
    {
        $data = $request->validate([
            'code'        => 'required|string|max:10|unique:currencies,code',
            'symbol'      => 'required|string|max:10',
            'name'        => 'required|string|max:80',
            'rate_to_ngn' => 'required|numeric|min:0.000001',
            'is_active'   => 'boolean',
            'sort_order'  => 'integer',
        ]);

        $data['is_base']   = false;
        $data['is_active'] = $request->boolean('is_active', true);
        $data['code']      = strtoupper($data['code']);

        Currency::create($data);

        return redirect()->route('admin.currencies.index')
                         ->with('success', 'Currency added successfully.');
    }

    /** GET /admin/currencies/{currency}/edit */
    public function edit(Currency $currency)
    {
        return view('admin.currencies.form', compact('currency'));
    }

    /** PUT /admin/currencies/{currency} */
    public function update(Request $request, Currency $currency)
    {
        $data = $request->validate([
            'symbol'      => 'required|string|max:10',
            'name'        => 'required|string|max:80',
            'rate_to_ngn' => 'required|numeric|min:0.000001',
            'is_active'   => 'boolean',
            'sort_order'  => 'integer',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);

        // Never let the base currency rate be changed
        if ($currency->is_base) {
            unset($data['rate_to_ngn']);
        }

        $currency->update($data);

        return redirect()->route('admin.currencies.index')
                         ->with('success', 'Currency updated.');
    }

    /** DELETE /admin/currencies/{currency} */
    public function destroy(Currency $currency)
    {
        if ($currency->is_base) {
            return back()->with('error', 'Cannot delete the base currency (NGN).');
        }

        $currency->delete();

        return redirect()->route('admin.currencies.index')
                         ->with('success', 'Currency removed.');
    }
}