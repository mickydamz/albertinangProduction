<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Category;
use App\Models\AuditLog;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $userCounts        = User::selectRaw("COUNT(*) as total, SUM(role = 'supplier') as suppliers")->first();
        $totalUsers        = (int) $userCounts->total;
        $totalSuppliers    = (int) $userCounts->suppliers;
        $totalTransactions = Transaction::count() + Order::count();
        $totalProducts     = Product::count();

        $recentActivity = AuditLog::with('user')
            ->whereHas('user', fn ($q) => $q->where('role', 'admin'))
            ->latest()
            ->limit(15)
            ->get();

        return view('admin.dashboard', compact(
            'totalUsers', 'totalSuppliers', 'totalTransactions', 'totalProducts',
            'recentActivity'
        ));
    }

    // ── Build last-12-months scaffold: labels + [year,month] pairs ──────────
    private function monthlyScaffold(): array
    {
        $labels = [];
        $months = [];
        for ($i = 11; $i >= 0; $i--) {
            $date     = now()->subMonths($i);
            $labels[] = $date->format('M y');
            $months[] = [$date->year, $date->month];
        }
        return [$labels, $months];
    }

    // ── One GROUP BY query → keyed by "year-month" for O(1) scaffold lookup ─
    private function monthlyRows(string $model, ?string $whereClause = null, string $aggregate = 'COUNT(*) as n'): \Illuminate\Support\Collection
    {
        $start = now()->subMonths(11)->startOfMonth();
        return $model::selectRaw("YEAR(created_at) as y, MONTH(created_at) as m, {$aggregate}")
            ->where('created_at', '>=', $start)
            ->when($whereClause, fn ($q) => $q->whereRaw($whereClause))
            ->groupByRaw('YEAR(created_at), MONTH(created_at)')
            ->get()
            ->keyBy(fn ($r) => $r->y . '-' . $r->m);
    }

    // ── New user registrations per month — 1 query ───────────────────────────
    public function getUserAnalytics()
    {
        [$labels, $months] = $this->monthlyScaffold();
        $rows   = $this->monthlyRows(User::class);
        $counts = array_map(fn ($m) => (int) ($rows["{$m[0]}-{$m[1]}"]->n ?? 0), $months);

        return response()->json([
            'labels'   => $labels,
            'datasets' => [[
                'label'                => 'New Users',
                'data'                 => $counts,
                'fill'                 => true,
                'tension'              => 0.4,
                'backgroundColor'      => 'rgba(59,130,246,0.12)',
                'borderColor'          => 'rgba(59,130,246,1)',
                'borderWidth'          => 2,
                'pointRadius'          => 3,
                'pointBackgroundColor' => 'rgba(59,130,246,1)',
            ]],
        ]);
    }

    // ── New suppliers per month — 1 query ────────────────────────────────────
    public function getSupplierAnalytics()
    {
        [$labels, $months] = $this->monthlyScaffold();
        $rows   = $this->monthlyRows(User::class, "role = 'supplier'");
        $counts = array_map(fn ($m) => (int) ($rows["{$m[0]}-{$m[1]}"]->n ?? 0), $months);

        return response()->json([
            'labels'   => $labels,
            'datasets' => [[
                'label'           => 'New Suppliers',
                'data'            => $counts,
                'backgroundColor' => 'rgba(139,92,246,0.7)',
                'borderColor'     => 'rgba(139,92,246,1)',
                'borderWidth'     => 1,
                'borderRadius'    => 4,
            ]],
        ]);
    }

    // ── Orders + transactions per month — 2 queries (was 24) ─────────────────
    public function getTransactionAnalytics()
    {
        [$labels, $months] = $this->monthlyScaffold();
        $txRows    = $this->monthlyRows(Transaction::class);
        $orderRows = $this->monthlyRows(Order::class);

        $counts = array_map(function ($m) use ($txRows, $orderRows) {
            $key = "{$m[0]}-{$m[1]}";
            return (int) ($txRows[$key]->n ?? 0) + (int) ($orderRows[$key]->n ?? 0);
        }, $months);

        return response()->json([
            'labels'   => $labels,
            'datasets' => [[
                'label'           => 'Transactions & Orders',
                'data'            => $counts,
                'backgroundColor' => 'rgba(239,68,68,0.7)',
                'borderColor'     => 'rgba(239,68,68,1)',
                'borderWidth'     => 1,
                'borderRadius'    => 4,
            ]],
        ]);
    }

    // ── Top-10 best-selling products by units ordered ─────────────────────────
    public function getProductAnalytics()
    {
        $rows = OrderItem::selectRaw('name, SUM(quantity) as total')
            ->groupBy('name')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        if ($rows->isNotEmpty()) {
            return response()->json([
                'labels'   => $rows->pluck('name')
                    ->map(fn ($n) => mb_strimwidth($n, 0, 28, '…'))
                    ->all(),
                'datasets' => [[
                    'label'           => 'Units Sold',
                    'data'            => $rows->pluck('total')->all(),
                    'backgroundColor' => 'rgba(16,185,129,0.7)',
                    'borderColor'     => 'rgba(16,185,129,1)',
                    'borderWidth'     => 1,
                    'borderRadius'    => 4,
                ]],
            ]);
        }

        // Fallback when no orders yet: products listed per category
        $cats = Category::withCount('products')
            ->orderByDesc('products_count')
            ->limit(10)
            ->get();

        return response()->json([
            'labels'   => $cats->pluck('name')->all(),
            'datasets' => [[
                'label'           => 'Products Listed',
                'data'            => $cats->pluck('products_count')->all(),
                'backgroundColor' => 'rgba(16,185,129,0.7)',
                'borderColor'     => 'rgba(16,185,129,1)',
                'borderWidth'     => 1,
                'borderRadius'    => 4,
            ]],
        ]);
    }

    // ── Revenue (order totals) per month — 1 query ───────────────────────────
    public function getRevenueAnalytics()
    {
        [$labels, $months] = $this->monthlyScaffold();
        $rows   = $this->monthlyRows(Order::class, null, 'SUM(total) as n');
        $totals = array_map(fn ($m) => (float) ($rows["{$m[0]}-{$m[1]}"]->n ?? 0), $months);

        return response()->json([
            'labels'   => $labels,
            'datasets' => [[
                'label'                => 'Revenue (₦)',
                'data'                 => $totals,
                'fill'                 => true,
                'tension'              => 0.4,
                'backgroundColor'      => 'rgba(78,122,26,0.12)',
                'borderColor'          => 'rgba(78,122,26,1)',
                'borderWidth'          => 2,
                'pointRadius'          => 3,
                'pointBackgroundColor' => 'rgba(78,122,26,1)',
            ]],
        ]);
    }

    // ── Orders grouped by status ──────────────────────────────────────────────
    public function getOrderStatusAnalytics()
    {
        $statuses = Order::selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->orderByDesc('total')
            ->get();

        $colorMap = [
            'pending'    => ['rgba(251,191,36,0.8)',  'rgba(251,191,36,1)'],
            'processing' => ['rgba(59,130,246,0.8)',  'rgba(59,130,246,1)'],
            'paid'       => ['rgba(16,185,129,0.8)',  'rgba(16,185,129,1)'],
            'shipped'    => ['rgba(139,92,246,0.8)',  'rgba(139,92,246,1)'],
            'delivered'  => ['rgba(34,197,94,0.8)',   'rgba(34,197,94,1)'],
            'completed'  => ['rgba(20,184,166,0.8)',  'rgba(20,184,166,1)'],
            'cancelled'  => ['rgba(239,68,68,0.8)',   'rgba(239,68,68,1)'],
            'failed'     => ['rgba(107,114,128,0.8)', 'rgba(107,114,128,1)'],
        ];

        $labels = [];
        $data   = [];
        $bg     = [];
        $border = [];

        foreach ($statuses as $row) {
            $labels[] = ucfirst($row->status);
            $data[]   = $row->total;
            $colors   = $colorMap[$row->status] ?? ['rgba(156,163,175,0.8)', 'rgba(156,163,175,1)'];
            $bg[]     = $colors[0];
            $border[] = $colors[1];
        }

        return response()->json([
            'labels'   => $labels,
            'datasets' => [[
                'label'           => 'Orders',
                'data'            => $data,
                'backgroundColor' => $bg,
                'borderColor'     => $border,
                'borderWidth'     => 2,
            ]],
        ]);
    }
}
