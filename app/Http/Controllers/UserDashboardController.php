<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\User;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\Banner;
use App\Models\Location;
use App\Models\Brand;
use Illuminate\Support\Facades\Cache;

class UserDashboardController extends Controller
{
    public function index(Request $request)
    {
        $locationFilter = $request->query('location');
        $currencyFilter = $request->query('currency', 'NGN');

        $banners = Banner::where('status', true)
            ->orderBy('sort_order')
            ->orderByDesc('created_at')
            ->get();

        // Cache key per location so filtered views are cached independently
        $ck = 'hp_' . ($locationFilter ?: 'all');

        // Best Selling — 16 newest active products
        $products = Cache::remember($ck . '_best', 900, function () use ($locationFilter) {
            $q = Product::where('is_active', true)
                ->with(['images', 'category', 'Subcategory', 'brand'])
                ->withReviewStats()
                ->latest();
            if ($locationFilter) {
                $q->whereHas('locations', fn ($query) => $query->where('name', $locationFilter));
            }
            return $q->take(16)->get();
        });

        // Featured — highest-rated products (distinct from Best Selling)
        $featured = Cache::remember($ck . '_featured', 900, function () use ($locationFilter) {
            $q = Product::where('is_active', true)
                ->with(['images', 'category', 'Subcategory', 'brand'])
                ->withReviewStats()
                ->orderByDesc('rating')
                ->orderByDesc('created_at');
            if ($locationFilter) {
                $q->whereHas('locations', fn ($query) => $query->where('name', $locationFilter));
            }
            return $q->take(16)->get();
        });

        // Televisions
        $televisions = Cache::remember($ck . '_tv', 900, function () use ($locationFilter) {
            $q = Product::where('is_active', true)
                ->whereHas('Subcategory', function ($query) {
                    $query->where('name', 'Televisions')
                          ->whereHas('category', fn ($q) => $q->where('name', 'Sound and Vision'));
                })
                ->with(['images', 'category', 'Subcategory', 'brand'])
                ->withReviewStats()
                ->latest();
            if ($locationFilter) {
                $q->whereHas('locations', fn ($query) => $query->where('name', $locationFilter));
            }
            return $q->take(16)->get();
        });

        // Air Conditioners
        $acs = Cache::remember($ck . '_ac', 900, function () use ($locationFilter) {
            $q = Product::where('is_active', true)
                ->whereHas('category', fn ($query) => $query->where('name', 'Air Cooling'))
                ->with(['images', 'category', 'Subcategory', 'brand'])
                ->withReviewStats()
                ->latest();
            if ($locationFilter) {
                $q->whereHas('locations', fn ($query) => $query->where('name', $locationFilter));
            }
            return $q->take(16)->get();
        });

        // Washing Machines
        $washingMachines = Cache::remember($ck . '_wm', 900, function () use ($locationFilter) {
            $q = Product::where('is_active', true)
                ->whereHas('category', fn ($query) => $query->where('name', 'Garment Care'))
                ->with(['images', 'category', 'Subcategory', 'brand'])
                ->withReviewStats()
                ->latest();
            if ($locationFilter) {
                $q->whereHas('locations', fn ($query) => $query->where('name', $locationFilter));
            }
            return $q->take(16)->get();
        });

        $categories = Category::where('is_active', true)->get();

        $topLevelCategories = Category::where('is_active', true)
            ->with(['subcategories' => function ($query) {
                $query->where('is_active', true);
            }])
            ->get();

        $locations = Location::all();
        $brands    = Brand::where('is_active', true)->get();

        $categoryIcons = [
            'Home Appliances'    => 'blender',
            'Kitchen Appliances' => 'kitchen-set',
            'Garment Care'       => 'washing-machine',
            'Sound and Vision'   => 'tv',
            'Air Cooling'        => 'fan',
            'Accessories'        => 'headphones',
        ];

        return view('user.dashboard', compact(
            'topLevelCategories',
            'products',
            'featured',
            'categories',
            'televisions',
            'acs',
            'banners',
            'washingMachines',
            'locations',
            'locationFilter',
            'currencyFilter',
            'categoryIcons',
            'brands'
        ));
    }

    /**
     * Invalidate the cached homepage product collections.
     *
     * The homepage caches its sections (keys `hp_<loc>_<section>`) for 900s,
     * so product edits are otherwise invisible until the cache expires. Call
     * this whenever a product is created, updated or deleted. The file cache
     * driver has no tag support, so every location variant is forgotten
     * explicitly ('all' plus each location name).
     */
    public static function clearHomepageCache(): void
    {
        $locations = Location::pluck('name')->push('all');
        $sections  = ['best', 'featured', 'tv', 'ac', 'wm'];

        foreach ($locations as $loc) {
            foreach ($sections as $section) {
                Cache::forget('hp_' . $loc . '_' . $section);
            }
        }
    }
}
