<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ManagerSalesController extends Controller
{
    private const STATUSES = ['pending', 'paid', 'processing', 'ready_for_pickup', 'shipped', 'delivered', 'completed', 'cancelled', 'refunded', 'failed'];

    public function index(Request $request)
    {
        $filters = $request->validate([
            'search' => 'nullable|string|max:150',
            'brand' => 'nullable|string|max:255',
            'status' => ['nullable', Rule::in(self::STATUSES)],
            'from' => 'nullable|date_format:Y-m-d',
            'to' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:from'],
        ]);

        // Exact IDs are authoritative. Legacy lines may use a globally unique SKU
        // or, without a SKU, a globally unique name. Ambiguous matches stay hidden.
        $query = DB::table('order_items as line')
            ->join('orders as sale', 'sale.id', '=', 'line.order_id')
            ->join('products as product', function ($join) {
                $join->whereRaw("(product.id = line.product_id OR (line.product_id IS NULL AND (
                    (line.sku IS NOT NULL AND line.sku <> '' AND product.sku = line.sku
                     AND (SELECT COUNT(*) FROM products AS candidates WHERE candidates.sku = line.sku) = 1)
                    OR ((line.sku IS NULL OR line.sku = '') AND product.name = line.name
                     AND (SELECT COUNT(*) FROM products AS candidates WHERE candidates.name = line.name) = 1)
                )))");
            })
            ->where('product.manager_id', $request->user()->id);

        $brands = DB::table('products')->where('manager_id', $request->user()->id)
            ->whereNotNull('brand')->where('brand', '<>', '')->distinct()->orderBy('brand')->pluck('brand');
        if (!empty($filters['brand'])) $query->where('product.brand', $filters['brand']);
        if (!empty($filters['status'])) $query->where('sale.status', $filters['status']);
        if (!empty($filters['from'])) $query->where('sale.created_at', '>=', $filters['from'].' 00:00:00');
        if (!empty($filters['to'])) $query->where('sale.created_at', '<', \Carbon\Carbon::parse($filters['to'])->addDay()->format('Y-m-d'));
        if (!empty($filters['search'])) {
            $term = '%'.$filters['search'].'%';
            $query->where(function ($q) use ($term) {
                $q->where('line.name', 'like', $term)->orWhere('product.name', 'like', $term)
                    ->orWhere('line.sku', 'like', $term)->orWhere('product.sku', 'like', $term)->orWhere('sale.order_number', 'like', $term);
            });
        }

        // Match the existing workspace's payment verification rules. These are
        // gross merchandise values, never the total of a mixed-brand order.
        $confirmed = "sale.status IN ('paid','processing','ready_for_pickup','shipped','delivered','completed') AND (COALESCE(sale.manager_created, 0) = 0 OR sale.payment_verified_at IS NOT NULL)";
        $summary = (clone $query)->selectRaw("COUNT(DISTINCT sale.id) AS orders_count,
            COALESCE(SUM(CASE WHEN $confirmed THEN line.quantity ELSE 0 END), 0) AS confirmed_units,
            COALESCE(SUM(CASE WHEN $confirmed THEN line.price * line.quantity ELSE 0 END), 0) AS confirmed_value,
            COUNT(DISTINCT CASE WHEN sale.status IN ('pending','paid','processing','ready_for_pickup','shipped') THEN sale.id END) AS current_orders")->first();
        $sales = $query->select([
            'line.id', 'line.name', 'line.sku', 'line.price', 'line.quantity', 'line.installation_option', 'line.installation_extra_ngn',
            'product.brand', 'sale.id as order_id', 'sale.order_number', 'sale.created_at', 'sale.status',
        ])->selectRaw("CASE WHEN $confirmed THEN 1 ELSE 0 END AS confirmed")
            ->orderByDesc('sale.created_at')->orderByDesc('line.id')->paginate(25)->withQueryString();

        $statuses = self::STATUSES;
        return view('manager.sales.index', compact('sales', 'summary', 'filters', 'brands', 'statuses'));
    }
}
