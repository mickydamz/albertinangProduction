<?php
namespace App\Support;

use App\Models\ManagedOrder;

class ManagerOrderAccess
{
    public static function query()
    {
        $query = ManagedOrder::query();
        if (auth()->user()->role === 'admin') return $query;
        $manager = auth()->id();
        // Assignment is an explicit grant. Otherwise any managed line grants
        // whole-order processing, including mixed-brand orders (owner policy).
        return $query->where(function ($q) use ($manager) {
            $q->where('assigned_manager_id', $manager)->orWhereExists(function ($lines) use ($manager) {
                $lines->selectRaw('1')->from('order_items as access_line')
                    ->join('products as access_product', function ($join) {
                        $join->whereRaw("(access_product.id = access_line.product_id OR (access_line.product_id IS NULL AND (
                            (access_line.sku IS NOT NULL AND access_line.sku <> '' AND access_product.sku = access_line.sku
                             AND (SELECT COUNT(*) FROM products AS candidates WHERE candidates.sku = access_line.sku) = 1)
                            OR ((access_line.sku IS NULL OR access_line.sku = '') AND access_product.name = access_line.name
                             AND (SELECT COUNT(*) FROM products AS candidates WHERE candidates.name = access_line.name) = 1)
                        )))");
                    })->whereColumn('access_line.order_id', 'orders.id')->where('access_product.manager_id', $manager);
            });
        });
    }

    public static function productFor(\App\Models\OrderItem $item): ?\App\Models\Product
    {
        if ($item->product_id) return \App\Models\Product::whereKey($item->product_id)->lockForUpdate()->first();
        $query=\App\Models\Product::query();
        if ($item->sku) $query->where('sku',$item->sku);
        elseif ($item->name) $query->where('name',$item->name);
        else return null;
        $matches=$query->lockForUpdate()->limit(2)->get();
        return $matches->count()===1?$matches->first():null;
    }

    public static function find(int $id, bool $lock = false): ManagedOrder
    {
        return static::query()->when($lock, fn($q) => $q->lockForUpdate())->findOrFail($id);
    }
}
