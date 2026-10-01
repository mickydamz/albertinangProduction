<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Tag;
use App\Models\Size;
use App\Models\Location;
use App\Models\Color;
use App\Models\Subcategory;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Brand;

class ProductController extends Controller
{
    // =========================================================================
    //  PRODUCT LIST (admin / internal)
    // =========================================================================

    public function index(Request $request)
    {
        $brandsParam = array_filter((array) $request->input('brands', []));
        if (!empty($brandsParam)) {
            $brandName = reset($brandsParam);
            $brand = Brand::where('name', $brandName)
                ->orWhere('slug', \Str::slug($brandName))
                ->first();
            if ($brand) {
                return redirect()->route('brand.show', $brand->slug ?: \Str::slug($brand->name));
            }
        }

        // Delegate to the search engine with no query — shows all products
        // with the full faceted sidebar and simslayout design.
        return $this->searchProducts($request);
    }

    // =========================================================================
    //  CART TRUCK-CHECK — re-verify requires_truck for a set of product IDs
    // =========================================================================

    public function truckCheck(Request $request)
    {
        $ids = array_filter(array_map('intval', explode(',', $request->query('ids', ''))));

        $thresholds = $this->truckThresholds();

        if (empty($ids)) {
            return response()->json(['products' => [], 'thresholds' => $thresholds]);
        }

        $products = Product::with(['category', 'Subcategory'])
            ->whereIn('id', $ids)
            ->get()
            ->mapWithKeys(function ($p) {
                // Trickle-down: product → subcategory → category → 5 kg default
                $weight = $p->weight_kg;

                if (is_null($weight) && $p->subcategory_id) {
                    $weight = $p->Subcategory?->estimated_weight_kg;
                }

                if (is_null($weight) && $p->category_id) {
                    $weight = $p->category?->estimated_weight_kg;
                }

                return [(string) $p->id => [
                    'requires_truck'     => $p->requires_truck,
                    'inferred_weight_kg' => $weight ?? 5.0,
                ]];
            });

        return response()->json([
            'products'   => $products,
            'thresholds' => $thresholds,
        ]);
    }

    private function truckThresholds(): array
    {
        return [
            'weight_kg'       => (float) \App\Models\Setting::get('truck_weight_threshold_kg', 30),
            'order_value_ngn' => (float) \App\Models\Setting::get('truck_order_value_threshold_ngn', 1000000),
        ];
    }

    // =========================================================================
    //  SINGLE PRODUCT PAGE
    // =========================================================================

    public function show(Request $request, $id)
    {
        $currencyFilter = $request->query('currency', 'NGN');

        $product = Product::with([
            'images',
            'category',
            'Subcategory',
            'locations',
            'reviews',
            'reviews.author',
        ])->findOrFail($id);

        // Related products — same subcategory first, fall back to category
        $relatedProducts = Product::with(['images'])
            ->where('is_active', true)
            ->where('id', '!=', $product->id)
            ->where(function ($q) use ($product) {
                if ($product->subcategory_id) {
                    $q->where('subcategory_id', $product->subcategory_id);
                } else {
                    $q->where('category_id', $product->category_id);
                }
            })
            ->latest()
            ->take(4)
            ->get();

        // Reviews & rating breakdown — use the already-eager-loaded collection
        $reviews            = $product->reviews->filter(fn ($r) => !is_null($r->rating));
        $averageRating      = $reviews->count() ? round($reviews->avg('rating'), 1) : 0;
        $ratingCount        = $reviews->count();
        $ratingDistribution = [];
        foreach ([5, 4, 3, 2, 1] as $star) {
            $ratingDistribution[$star] = $reviews->where('rating', $star)->count();
        }

        // ── Has the logged-in user purchased this product? ──────────────────
        // Controls whether the "Write a Review" form shows on the page.
        $hasPurchased = false;

        if (auth()->check()) {
            $hasPurchased = \App\Models\Order::where('user_id', auth()->id())
                ->whereHas('items', function ($q) use ($product) {
                    $q->where('product_id', $product->id);
                })
                // Only count orders that actually went through.
                // Adjust these to match the values in your orders.status column.
                ->whereIn('status', ['paid', 'processing', 'shipped', 'completed', 'delivered'])
                ->exists();
        }

        $topLevelCategories = Cache::remember('nav_categories', 3600, fn () => Category::with('subcategories')->get());
        $locations          = Cache::remember('ref_locations',  3600, fn () => Location::all());
        $categoryIcons      = [
            'Home Appliances'    => 'blender',
            'Kitchen Appliances' => 'kitchen-set',
            'Garment Care'       => 'washing-machine',
            'Sound and Vision'   => 'tv',
            'Air Cooling'        => 'fan',
            'Accessories'        => 'headphones',
        ];
        $locationFilter = $request->query('location');

        // ── Has the logged-in user already reviewed this product? ──────────
        // Controls whether the "Write a Review" form shows (one review per user).
        $hasReviewed = false;

        if (auth()->check()) {
            $hasReviewed = $product->reviews->where('user_id', auth()->id())->isNotEmpty();
        }

        return view('products.show', compact(
            'product',
            'relatedProducts',
            'topLevelCategories',
            'locations',
            'locationFilter',
            'currencyFilter',
            'categoryIcons',
            'averageRating',
            'ratingCount',
            'ratingDistribution',
            'hasPurchased',
            'hasReviewed'
        ));
    }

    // =========================================================================
    //  CATEGORY PAGE
    // =========================================================================

    public function showCategory(Request $request, $categoryName)
    {
        Log::info('showCategory Request Parameters:', $request->all());

        // ── Currency ────────────────────────────────────────────────────────
        $currentCurrency = strtoupper($request->query('currency', session('currency', 'NGN')));
        if (!in_array($currentCurrency, ['NGN', 'USD', 'GBP', 'EUR', 'CAD'])) {
            $currentCurrency = 'NGN';
        }
        session(['currency' => $currentCurrency]);

        // ── Resolve category or subcategory ───────────────────────────────────
        // Hyphens in the URL slug → spaces; collapse any runs of whitespace.
        $normalizedCategoryName = trim(
            preg_replace('/\s+/', ' ', str_replace('-', ' ', strtolower($categoryName)))
        );

        // Primary: exact lowercase match.
        // Fallback: DB name may contain special chars (/, &, +) that were
        // converted to hyphens by Str::slug; strip them on the DB side too.
        // Only load subcategories (not their products) — product scoping is
        // done via a direct WHERE clause, avoiding a double product load.
        $category = Category::where(function ($q) use ($normalizedCategoryName) {
            $q->whereRaw('LOWER(name) = ?', [$normalizedCategoryName])
              ->orWhereRaw(
                  "LOWER(REPLACE(REPLACE(REPLACE(name, '/', ' '), '&', ' '), '+', ' ')) = ?",
                  [$normalizedCategoryName]
              );
        })->with(['subcategories'])->first();

        $productsQuery              = Product::where('is_active', true);
        $currentCategoryDisplayName = $categoryName;
        $Subcategory                = null;
        $parentCategory             = null;

        if ($category) {
            $subcategoryIds = $category->subcategories->pluck('id');
            $productsQuery->where(function ($q) use ($category, $subcategoryIds) {
                $q->where('category_id', $category->id)
                  ->orWhereIn('subcategory_id', $subcategoryIds);
            });
            $currentCategoryDisplayName = $category->name;
        } else {
            // Try matching as a subcategory — only need parent category for breadcrumb
            $Subcategory = Subcategory::where(function ($q) use ($normalizedCategoryName) {
                $q->whereRaw('LOWER(name) = ?', [$normalizedCategoryName])
                  ->orWhereRaw(
                      "LOWER(REPLACE(REPLACE(REPLACE(name, '/', ' '), '&', ' '), '+', ' ')) = ?",
                      [$normalizedCategoryName]
                  );
            })->with(['category'])->first();

            if ($Subcategory) {
                $productsQuery->where('subcategory_id', $Subcategory->id);
                $currentCategoryDisplayName = $Subcategory->name;
                $parentCategory             = $Subcategory->category;
            } else {
                abort(404, "Category or Subcategory '$categoryName' not found");
            }
        }

        // ── Fetch the scoped collection ONCE, with all relations ──────────────
        // Everything below (facets, price bounds, filtering) derives from this
        // single in-memory collection. No N+1, no repeated ->get().
        $scoped = $this->buildScopedCollection($productsQuery);

        // ── Static sidebar data (brands list, lookup tables) ──────────────────
        $brands = $scoped->pluck('brand')->filter()->unique()->sort()->values()->toArray();

        $topLevelCategories = Cache::remember('nav_categories', 3600, fn () => Category::with('subcategories')->get());
        $tags               = Cache::remember('ref_tags',       3600, fn () => Tag::all());
        $sizes              = Cache::remember('ref_sizes',      3600, fn () => Size::all());
        $colors             = Cache::remember('ref_colors',     3600, fn () => Color::all());
        $locations          = Cache::remember('ref_locations',  3600, fn () => Location::all());

        // ── Collapse near-duplicate values into one label per attribute ───────
        // Build the raw→label maps BEFORE counting so "QLED (Quantum Dot LED)"
        // and "QLED ... / VA Panel" fold into a single "QLED" option.
        $this->primeBucketLabels($scoped);

        // ── Mandatory (pinned) facets ─────────────────────────────────────────
        // Keyword-route inconsistent attribute keys (screen_size / size /
        // display_size …) into one synthetic facet per the category map, so the
        // "inevitable" filters are always present and never pruned. Must run
        // AFTER primeBucketLabels (uses resolveLabel) and BEFORE facetCounts.
        $resolvedCatName = $category->name ?? ($parentCategory->name ?? null);
        $resolvedSubName = $Subcategory->name ?? null;
        $this->primeMandatoryFacets($scoped, $resolvedCatName, $resolvedSubName);

        // ── LIVE FACET COUNTS (the smart bit) ─────────────────────────────────
        // Each axis is counted against products matching every OTHER selected
        // filter — so ticking "4K" updates "HDR" counts, etc.
        $facets = $this->facetCounts($scoped, $request);

        // Baseline (unfiltered) counts — used to rank which values are worth
        // offering as filters.
        $baseline = $this->baselineCustomCounts($scoped);

        // ── Build the displayable custom-attribute option lists ───────────────
        // RULE: per attribute, keep only the highest-count values and drop the
        // long tail. With baseline counts {1, 2, 5, 8} and $topN = 2, only the
        // 5 and 8 values show. Selected values are always kept (topValuesByCount).
        // Mandatory facets bypass the cap so their full range survives.
        $topN = 5;
        $customOptionsForFilter = [];
        foreach ($facets['custom'] as $key => $valueCounts) {
            $selectedForKey = (array) request("options.$key", []);
            $limit  = $this->isMandatoryFacet($key) ? 50 : $topN;

            // Only rank values shared by 2+ products — single-product values
            // can't help a shopper narrow anything (e.g. "1.02 Litres (1)").
            $baseForKey = $baseline[$key] ?? $valueCounts;
            $rankable   = array_filter($baseForKey, fn ($c) => $c >= 2);
            $values     = $this->topValuesByCount($rankable, $selectedForKey, $limit);

            // Always show a value the user already selected, even if it
            // slipped below the threshold (so they can deselect it).
            foreach ($selectedForKey as $sel) {
                $sel = (string) $sel;
                if ($sel !== '' && !in_array($sel, $values, true)) {
                    $values[] = $sel;
                }
            }

            if (empty($values)) continue;
            if (count($values) < 2 && empty($selectedForKey)) continue;
            $customOptionsForFilter[$key] = $values;
        }

        // Drop any custom section that is really a duplicate of the Brands
        // facet — i.e. whose values are brand names. Catches brand-like keys
        // regardless of what they are named in custom_attributes.
        $customOptionsForFilter = $this->dropBrandDuplicateSections($customOptionsForFilter, $brands);

        // ── Keep only the strongest attribute SECTIONS ────────────────────────
        // Too many sections (even at 2 values each) makes the sidebar endless.
        // Rank attributes by total products covered by their shown values and
        // keep the top $maxSections. Any attribute the user has an active
        // filter on is always kept so it can be seen and removed. Mandatory
        // facets are always kept and floated to the top.
        $maxSections = 6;
        $customOptionsForFilter = $this->topSections(
            $customOptionsForFilter,
            $baseline,
            $request,
            $maxSections
        );

        // ── Price bounds for the sidebar hint (full scoped range) ─────────────
        //
        // Computed across ALL products in the category (before user filters),
        // so the hint always shows the full available range.
        //
        // sell_price already includes:
        //   base price → markup (product → subcategory → category)
        //             → discount (product → subcategory → category)
        $priceMin = $scoped->min(fn ($p) => $p->sell_price) ?? 0;
        $priceMax = $scoped->max(fn ($p) => $p->sell_price) ?? 0;

        // ── Apply ALL filters (non-price + price) in PHP, then sort + paginate ─
        $sortBy   = $request->input('sort_by', 'popularity');
        $filtered = $this->applyFiltersInPhp($scoped, $request, $sortBy);

        $filteredProducts = $this->paginateCollection($filtered, $request);

        return view('category', [
            'categoryName'           => $currentCategoryDisplayName,
            'parentCategory'         => $parentCategory,
            'currentCurrency'        => $currentCurrency,
            'topLevelCategories'     => $topLevelCategories,
            'tags'                   => $tags,
            'sizes'                  => $sizes,
            'colors'                 => $colors,
            'locations'              => $locations,
            'categories'             => $topLevelCategories,
            'categoryProducts'       => $filteredProducts,
            'subcategories'          => $category ? $category->subcategories : collect(),
            'customOptionsForFilter' => $customOptionsForFilter,
            'customCounts'           => $facets['custom'],
            'brandCounts'            => $facets['brands'],
            'availabilityCounts'     => $facets['availability'],
            'brands'                 => $brands,
            'filterLabels'           => $this->facetLabels,
            'priceMin'               => $priceMin,
            'priceMax'               => $priceMax,
        ]);
    }

    // =========================================================================
    //  SEARCH PAGE
    // =========================================================================

    public function searchProducts(Request $request)
    {
        Log::info('searchProducts Request Parameters:', $request->all());

        $searchTerm = trim(urldecode($request->input('searchTerm', '')));
        $isWildcard = $searchTerm === '' || $searchTerm === '*';

        // Reject very short non-wildcard queries before hitting the DB
        if (!$isWildcard && strlen($searchTerm) < 2) {
            return view('products.search', [
                'searchTerm'             => $searchTerm,
                'products'               => new LengthAwarePaginator([], 0, 20),
                'topLevelCategories'     => Category::whereNull('parent_id')->with('subcategories')->get(),
                'scopedCategories'       => collect(),
                'brands'                 => [],
                'customOptionsForFilter' => [],
                'customCounts'           => [],
                'brandCounts'            => [],
                'availabilityCounts'     => ['in-stock' => 0, 'pre-order' => 0],
                'categoryCounts'         => [],
                'currentCurrency'        => 'NGN',
                'error'                  => 'Please enter at least 2 characters to search.',
            ]);
        }

        // ── Currency ────────────────────────────────────────────────────────
        $currentCurrency = strtoupper($request->query('currency', session('currency', 'NGN')));
        if (!in_array($currentCurrency, ['NGN', 'USD', 'GBP', 'EUR', 'CAD'])) {
            $currentCurrency = 'NGN';
        }
        session(['currency' => $currentCurrency]);

        // ── Build search query (text match only — no user filters yet) ────────
        $productsQuery = Product::where('is_active', true);

        if (!$isWildcard) {
            $productsQuery->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', $searchTerm . '%')
                  ->orWhere('name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('brand', 'like', '%' . $searchTerm . '%')
                  ->orWhereHas('category',    fn ($q) => $q->where('name', 'like', '%' . $searchTerm . '%'))
                  ->orWhereHas('Subcategory', fn ($q) => $q->where('name', 'like', '%' . $searchTerm . '%'));

                if (strlen($searchTerm) >= 4) {
                    $q->orWhere('description', 'like', '%' . $searchTerm . '%');
                }
                if (strlen($searchTerm) >= 3) {
                    $q->orWhereHas('tags', fn ($q) => $q->where('name', 'like', '%' . $searchTerm . '%'));
                }
            });

            $productsQuery->orderByRaw("
                CASE
                    WHEN name LIKE ? THEN 0
                    WHEN name LIKE ? THEN 1
                    WHEN brand LIKE ? THEN 2
                    ELSE 3
                END
            ", [
                $searchTerm . '%',
                '%' . $searchTerm . '%',
                '%' . $searchTerm . '%',
            ]);
        }

        $displayTerm = $isWildcard ? '' : $searchTerm;

        // ── Fetch scoped collection ONCE ──────────────────────────────────────
        // Preserves the relevance ORDER BY from the query above.
        $scoped = $this->buildScopedCollection($productsQuery);

        // ── Static sidebar data ───────────────────────────────────────────────
        $brands = $scoped->pluck('brand')->filter()->unique()->sort()->values()->toArray();

        $scopedCategories = Category::whereIn(
            'id',
            $scoped->pluck('category_id')->filter()->unique()
        )->orderBy('name')->get();

        $topLevelCategories = Cache::remember('nav_categories', 3600, fn () => Category::with('subcategories')->get());

        // ── Collapse near-duplicate values into one label per attribute ───────
        $this->primeBucketLabels($scoped);

        // ── Mandatory (pinned) facets ─────────────────────────────────────────
        // Search spans categories, so there's no single category. Passing nulls
        // falls back to '__default__', surfacing a generic size/capacity facet
        // wherever products carry a matching attribute (harmless otherwise — the
        // section simply won't render). To disable on search, set the
        // '__default__' entry to [] in $facetGroupsByCategory.
        $this->primeMandatoryFacets($scoped, null, null);

        // ── LIVE FACET COUNTS ─────────────────────────────────────────────────
        $facets = $this->facetCounts($scoped, $request);

        // Baseline (unfiltered) counts to rank which values are worth showing.
        $baseline = $this->baselineCustomCounts($scoped);

        // Build custom attribute filter options.
        // RULE: per attribute, keep only the top $topN values by baseline count;
        // drop the long tail of low-count one-offs. Selected values are kept.
        // Overly long free-text values (> 50 chars) are excluded up front.
        // Mandatory facets bypass the cap.
        $topN = 5;
        $customOptionsForFilter = [];
        foreach ($facets['custom'] as $key => $valueCounts) {
            $selectedForKey = (array) request("options.$key", []);
            $limit  = $this->isMandatoryFacet($key) ? 50 : $topN;

            // Require count >= 2 (shared by 2+ products) and value length <= 50.
            $rankable = array_filter(
                $baseline[$key] ?? $valueCounts,
                fn ($c, $v) => $c >= 2 && strlen((string) $v) <= 50,
                ARRAY_FILTER_USE_BOTH
            );
            $values = $this->topValuesByCount($rankable, $selectedForKey, $limit);

            // Always show a value the user already selected so they can deselect it.
            foreach ($selectedForKey as $sel) {
                $sel = (string) $sel;
                if ($sel !== '' && !in_array($sel, $values, true)) {
                    $values[] = $sel;
                }
            }

            if (empty($values)) continue;
            if (count($values) < 2 && empty($selectedForKey)) continue;
            $customOptionsForFilter[$key] = $values;
        }

        // Drop brand-duplicate sections (see showCategory).
        $customOptionsForFilter = $this->dropBrandDuplicateSections($customOptionsForFilter, $brands);

        // Keep only the strongest attribute sections (see showCategory).
        $maxSections = 6;
        $customOptionsForFilter = $this->topSections(
            $customOptionsForFilter,
            $baseline,
            $request,
            $maxSections
        );

        // ── Apply filters + sort + paginate ───────────────────────────────────
        // On search: keep relevance order unless wildcard (then honour sort_by).
        $sortBy    = $request->input('sort_by', 'popularity');
        $applySort = $isWildcard ? $sortBy : 'relevance';
        $filtered  = $this->applyFiltersInPhp($scoped, $request, $applySort);

        $products = $this->paginateCollection($filtered, $request);

        return view('products.search', [
            'searchTerm'             => $displayTerm,
            'products'               => $products,
            'topLevelCategories'     => $topLevelCategories,
            'scopedCategories'       => $scopedCategories,
            'brands'                 => $brands,
            'customOptionsForFilter' => $customOptionsForFilter,
            'customCounts'           => $facets['custom'],
            'brandCounts'            => $facets['brands'],
            'availabilityCounts'     => $facets['availability'],
            'categoryCounts'         => $facets['categories'],
            'currentCurrency'        => $currentCurrency,
            'filterLabels'           => $this->facetLabels,
        ]);
    }

    // =========================================================================
    //  BRAND PAGE
    // =========================================================================

    public function showBrand(Request $request, $brandSlug)
    {
        $brand = Brand::where('slug', $brandSlug)->orWhere('name', urldecode($brandSlug))->first();

        if (!$brand) {
            abort(404);
        }

        $currentCurrency = strtoupper($request->query('currency', session('currency', 'NGN')));
        if (!in_array($currentCurrency, ['NGN', 'USD', 'GBP', 'EUR', 'CAD'])) {
            $currentCurrency = 'NGN';
        }
        session(['currency' => $currentCurrency]);

        $productsQuery = Product::where('is_active', true)
            ->where('brand', $brand->name);

        $scoped = $this->buildScopedCollection($productsQuery);

        $scopedCategories = Category::whereIn(
            'id',
            $scoped->pluck('category_id')->filter()->unique()
        )->orderBy('name')->get();

        $this->primeBucketLabels($scoped);
        $this->primeMandatoryFacets($scoped, null, null);

        $facets   = $this->facetCounts($scoped, $request);
        $baseline = $this->baselineCustomCounts($scoped);

        $topN = 5;
        $customOptionsForFilter = [];
        foreach ($facets['custom'] as $key => $valueCounts) {
            $selectedForKey = (array) request("options.$key", []);
            $limit = $this->isMandatoryFacet($key) ? 50 : $topN;

            $rankable = array_filter(
                $baseline[$key] ?? $valueCounts,
                fn ($c, $v) => $c >= 2 && strlen((string) $v) <= 50,
                ARRAY_FILTER_USE_BOTH
            );
            $values = $this->topValuesByCount($rankable, $selectedForKey, $limit);

            foreach ($selectedForKey as $sel) {
                $sel = (string) $sel;
                if ($sel !== '' && !in_array($sel, $values, true)) {
                    $values[] = $sel;
                }
            }

            if (empty($values)) continue;
            if (count($values) < 2 && empty($selectedForKey)) continue;
            $customOptionsForFilter[$key] = $values;
        }

        $maxSections = 6;
        $customOptionsForFilter = $this->topSections(
            $customOptionsForFilter,
            $baseline,
            $request,
            $maxSections
        );

        $priceMin = $scoped->min(fn ($p) => $p->sell_price) ?? 0;
        $priceMax = $scoped->max(fn ($p) => $p->sell_price) ?? 0;

        $sortBy   = $request->input('sort_by', 'popularity');
        $filtered = $this->applyFiltersInPhp($scoped, $request, $sortBy);
        $products = $this->paginateCollection($filtered, $request);

        return view('products.brand', [
            'brand'                  => $brand,
            'products'               => $products,
            'scopedCategories'       => $scopedCategories,
            'customOptionsForFilter' => $customOptionsForFilter,
            'customCounts'           => $facets['custom'],
            'brandCounts'            => $facets['brands'],
            'availabilityCounts'     => $facets['availability'],
            'categoryCounts'         => $facets['categories'],
            'currentCurrency'        => $currentCurrency,
            'filterLabels'           => $this->facetLabels,
            'priceMin'               => $priceMin,
            'priceMax'               => $priceMax,
        ]);
    }

    // =========================================================================
    //  SEARCH SUGGESTIONS (AJAX)
    // =========================================================================

    public function getSearchSuggestions(Request $request)
    {
        $query       = $request->input('query');
        $suggestions = [];

        if ($query) {
            $productSuggestions = Product::where('is_active', true)
                ->where('name', 'like', '%' . $query . '%')
                ->with('images')
                ->limit(5)
                ->get(['id', 'name'])
                ->map(fn ($p) => [
                    'id'    => $p->id,
                    'name'  => $p->name,
                    'type'  => 'product',
                    // Thumbnail for the suggestion row (null → the UI shows an icon).
                    'image' => $p->images->isNotEmpty()
                        ? asset('storage/' . $p->images->first()->image_url)
                        : null,
                ])
                ->toArray();

            $categorySuggestions = Category::where('name', 'like', '%' . $query . '%')
                ->limit(3)
                ->pluck('name')
                ->map(fn ($name) => ['name' => $name, 'type' => 'category', 'image' => null])
                ->toArray();

            $suggestions = array_merge($productSuggestions, $categorySuggestions);
            usort($suggestions, fn ($a, $b) => strcmp($a['name'], $b['name']));
        }

        return response()->json($suggestions);
    }

    // =========================================================================
    //  FACETING + FILTERING HELPERS
    // =========================================================================

    /**
     * Fetch the scoped product collection once, with every relation the
     * sell_price / old_price accessors and the facet logic need.
     *
     * The accessor cascade (product → subcategory → category) means
     * category + Subcategory MUST be eager-loaded or every sell_price call
     * fires N+1 queries.
     */
    private function buildScopedCollection($query)
    {
        return $query
            ->with(['images', 'tags', 'category', 'Subcategory', 'sizes'])
            ->withReviewStats()
            ->get();
    }

    /**
     * Facet value aliases — fold synonymous raw attribute values into one
     * display label. Keyed by attribute, then [canonical label => [raw aliases]].
     *
     * Matching is case-insensitive (we compare lowercased/trimmed strings),
     * so you only need to list genuine spelling/wording variants, not case.
     *
     * Effect:
     *   • Counts: every alias's count sums into the canonical label.
     *   • Display: the sidebar shows only the canonical label.
     *   • Filtering: ticking the canonical label matches a product whose raw
     *     value is ANY of the aliases.
     *
     * Add more groups/values here as you spot duplicates in your data.
     */
    private array $facetAliases = [
        'resolution' => [
            '4K Ultra HD'    => ['4k', '4k ultra hd', 'ultra hd', 'uhd', '2160p'],
            'Full HD (1080p)'=> ['full hd', '1080p', 'fhd', 'full high definition'],
            'HD Ready (720p)'=> ['hd', 'hd ready', '720p'],
            '8K'             => ['8k', '8k ultra hd', '4320p'],
        ],
        // 'screen_type' => [
        //     'OLED' => ['oled', 'o-led'],
        //     'QLED' => ['qled', 'q-led'],
        // ],
    ];

    /**
     * Mandatory ("pinned") facet definitions, keyed by category or subcategory
     * name (matched case-insensitively, subcategory wins over category, with a
     * '__default__' fallback — same resolution order as the spec-tab blade).
     *
     * Each entry maps a SYNTHETIC facet key → the keywords that, when found in
     * a product's custom_attribute KEY, route that attribute's value into this
     * facet. This is what merges screen_size / size / display_size into one
     * "Screen Size" section regardless of how the data was entered.
     *
     * Keep this list short: only the headline filters a shopper expects for
     * the category, not every spec. Synthetic keys should be snake_case so the
     * sidebar header (ucwords + underscore→space) reads nicely.
     *
     * To disable mandatory facets on the search page, set '__default__' => [].
     */
    private array $facetGroupsByCategory = [
        'Televisions' => [
            'screen_size' => ['screen size', 'display size', 'size', 'inch', 'diagonal', 'panel size'],
            'resolution'  => ['resolution', 'display resolution', 'picture resolution'],
        ],
        'Split Ac' => [
            'capacity_btu' => ['btu', 'cooling capacity', 'rated cooling', 'capacity'],
            'inverter'     => ['inverter'],
        ],
        'Floor Standing AC' => [
            'capacity_btu' => ['btu', 'cooling capacity', 'rated cooling', 'capacity'],
            'inverter'     => ['inverter'],
        ],
        'Chest Freezers' => [
            'capacity' => ['capacity', 'volume', 'litre', 'liter', 'gross capacity', 'net capacity'],
        ],
        'Washing Machines' => [
            'load_capacity' => ['load', 'capacity', 'drum', 'kg'],
            'wash_type'     => ['front load', 'top load', 'loading', 'load type'],
        ],
        'Generators' => [
            'power_output' => ['kva', 'kw', 'rated power', 'max power', 'power output', 'output power'],
            'fuel_type'    => ['fuel', 'petrol', 'diesel', 'lpg', 'dual fuel'],
        ],
        'Standing Fans' => [
            'blade_size' => ['blade span', 'blade diameter', 'size', 'inch'],
        ],

        // No fallback. An unrecognised category (or the search page) may span
        // multiple product types whose specs are incompatible — mixing AC BTUs,
        // fridge litres, and washer kilograms into one "Capacity" filter is
        // worse than showing nothing. Specific categories get their own entry.
        '__default__' => [],
    ];

    /**
     * Per-request cache of [attributeKey => [rawValue => collapsedLabel]],
     * built once by primeBucketLabels() from the scoped collection. Used by
     * resolveLabel() so counting, display and matching all agree on which
     * raw values fold into which display label.
     */
    private array $bucketLabels = [];

    /**
     * Per-request map built by primeMandatoryFacets():
     *
     *   [ productId => [ syntheticKey => [collapsedLabel, collapsedLabel, ...] ] ]
     *
     * Lets matchesFilters() and facetCounts() answer "does this product carry
     * value X in synthetic facet K?" without re-scoring keywords each time.
     */
    private array $mandatoryFacetIndex = [];

    /**
     * The resolved synthetic keys for THIS request's category/subcategory,
     * e.g. ['screen_size', 'resolution']. These are the keys we force-keep.
     */
    private array $mandatoryFacetKeys = [];

    /**
     * Normalised (lowercase-trimmed) raw attribute keys that were absorbed into
     * a mandatory synthetic facet by primeMandatoryFacets(). These keys must be
     * skipped when counting real custom attributes so the same data doesn't
     * appear twice — once under the raw key and once under the synthetic key.
     */
    private array $absorbedAttributeKeys = [];

    /**
     * Human-readable sidebar headings for synthetic (mandatory) facet keys.
     * Real custom-attribute keys use ucwords(str_replace('_',' ',$key)) as
     * fallback since they're usually already stored in title-case form.
     */
    private array $facetLabels = [
        'screen_size'   => 'Screen Size',
        'resolution'    => 'Resolution',
        'capacity_btu'  => 'Capacity (BTU)',
        'capacity'      => 'Capacity',
        'inverter'      => 'Inverter',
        'load_capacity' => 'Load Capacity',
        'wash_type'     => 'Wash Type',
        'blade_size'    => 'Blade Size',
        'power_output'  => 'Power Output',
        'fuel_type'     => 'Fuel Type',
    ];

    /**
     * Custom-attribute keys that must never appear as their own filter section.
     *
     * Matched case-insensitively (see isExcludedAttribute).
     */
    private array $excludedAttributeKeys = [
        // Internal / system
        'tag_options',
        // Already shown in the dedicated Brands facet
        'brand', 'brands',
        // Product identity — always unique, never filterable
        'model', 'model number', 'model no', 'model no.',
        'part number', 'part no', 'part no.',
        'ean', 'ean code', 'barcode', 'sku', 'mpn', 'item code',
        // Physical dimensions — too specific per SKU
        'dimensions', 'dimension', 'net dimensions', 'gross dimensions',
        'product dimensions', 'package dimensions', 'box dimensions',
        'width', 'height', 'depth', 'length',
        // Weight — too specific; capacity is handled via mandatory facets
        'weight', 'net weight', 'gross weight', 'product weight',
        'shipping weight', 'package weight',
        // Box / accessories copy
        'box contents', 'package contents', "what's in the box",
        'whats in the box', 'in the box', 'accessories included', 'accessories',
    ];

    /**
     * Should this custom-attribute key be skipped when building facets?
     */
    private function isExcludedAttribute(string $key): bool
    {
        return in_array(strtolower(trim($key)), $this->excludedAttributeKeys, true);
    }

    /**
     * Was this raw attribute key routed into a mandatory synthetic facet?
     * Absorbed keys must not also appear as their own filter section.
     */
    private function isAbsorbedAttribute(string $key): bool
    {
        return isset($this->absorbedAttributeKeys[strtolower(trim($key))]);
    }

    /**
     * Build the raw→label collapse maps for every attribute, from the scoped
     * collection. Must be called once per request BEFORE facetCounts() /
     * baselineCustomCounts() / any matching. Idempotent.
     */
    private function primeBucketLabels($products): void
    {
        // Tally raw value frequencies per attribute (no collapsing yet).
        $rawCounts = []; // key => [rawValue => count]
        foreach ($products as $p) {
            if (empty($p->custom_attributes) || !is_array($p->custom_attributes)) {
                continue;
            }
            foreach ($p->custom_attributes as $key => $value) {
                if ($this->isExcludedAttribute($key)) continue;
                foreach ((array) $value as $val) {
                    $val = (string) $val;
                    if ($val === '') continue;
                    $rawCounts[$key][$val] = ($rawCounts[$key][$val] ?? 0) + 1;
                }
            }
        }

        $this->bucketLabels = [];
        foreach ($rawCounts as $key => $valueCounts) {
            $this->bucketLabels[$key] = $this->buildBucketLabels($key, $valueCounts);
        }
    }

    /**
     * Resolve a raw value to its collapsed display label using the primed map,
     * falling back to the alias map and finally the raw value itself.
     */
    private function resolveLabel(string $key, string $value): string
    {
        if (isset($this->bucketLabels[$key][$value])) {
            return $this->bucketLabels[$key][$value];
        }
        return $this->canonical($key, $value);
    }


    /**
     * Resolve a raw attribute value to its canonical display label.
     * Returns the original value untouched if no alias matches.
     */
    private function canonical(string $key, string $value): string
    {
        $needle = strtolower(trim($value));
        foreach ($this->facetAliases[$key] ?? [] as $canonical => $aliases) {
            foreach ($aliases as $alias) {
                if (strtolower(trim($alias)) === $needle) {
                    return $canonical;
                }
            }
        }
        return $value;
    }

    /**
     * Derive an automatic grouping key from a free-text value so near-
     * duplicates collapse into one bucket.
     *
     * The catalogue stores values like:
     *   "QLED (Quantum Dot LED)", "QLED (Quantum Dot LED) / VA",
     *   "QLED (Quantum Dot LED) / VA Panel"
     * which are really one option — QLED — with trailing qualifiers.
     *
     * Strategy: take the leading portion of the string, before the first
     * qualifier delimiter ( "(", "/", ",", "-", ":" ), then lowercase and
     * squeeze whitespace. All three QLED strings above reduce to "qled".
     * Values with no delimiter (e.g. "60Hz") just normalise to themselves.
     *
     * This is intentionally blunt — it groups by the head term, which works
     * well for "TYPE (detail)" style values. It is NOT applied to attributes
     * where the qualifier is the meaningful part; if you hit such a case,
     * exclude that key in buildBucketLabels().
     */
    private function bucketKey(string $value): string
    {
        // Cut at the first qualifier delimiter.
        $head = preg_split('/[\(\/,:\-]/', $value, 2)[0] ?? $value;
        // Lowercase + collapse internal whitespace for stable grouping.
        $head = strtolower(trim(preg_replace('/\s+/', ' ', $head)));
        return $head !== '' ? $head : strtolower(trim($value));
    }

    /**
     * For one attribute, build a map of raw value → collapsed display label.
     *
     * Groups every raw value by bucketKey(), then within each bucket picks the
     * MOST COMMON raw string as the human-readable label for the whole group.
     * Ties on frequency break toward the shortest string (usually the cleanest
     * form, e.g. "QLED (Quantum Dot LED)" over its "... / VA Panel" sibling),
     * then alphabetically.
     *
     * Hand-written aliases (canonical()) take precedence: if a value already
     * maps to an alias, that wins and it is grouped under the alias label.
     *
     * @param  string $key          the attribute key
     * @param  array  $rawValueCounts  [rawValue => count] for this attribute
     * @return array  [rawValue => displayLabel]
     */
    private function buildBucketLabels(string $key, array $rawValueCounts): array
    {
        // Group raw values into buckets.
        $buckets = []; // bucketId => [ rawValue => count ]
        foreach ($rawValueCounts as $raw => $count) {
            $raw = (string) $raw;

            // Alias map wins outright — its canonical label is its own bucket.
            $aliased = $this->canonical($key, $raw);
            if ($aliased !== $raw) {
                $buckets['__alias__' . $aliased][$raw] = $count;
                continue;
            }

            $buckets[$this->bucketKey($raw)][$raw] = $count;
        }

        // For each bucket choose the display label.
        $map = []; // rawValue => label
        foreach ($buckets as $bucketId => $members) {
            if (str_starts_with($bucketId, '__alias__')) {
                $label = substr($bucketId, strlen('__alias__'));
            } else {
                // Most common raw string wins; tie → shortest, then alphabetical.
                $candidates = collect($members)
                    ->map(fn ($count, $value) => [
                        'value' => (string) $value,
                        'count' => (int) $count,
                    ])
                    ->values()
                    ->all();
                usort($candidates, function ($a, $b) {
                    if ($a['count'] !== $b['count']) {
                        return $b['count'] <=> $a['count'];          // most common first
                    }
                    if (strlen($a['value']) !== strlen($b['value'])) {
                        return strlen($a['value']) <=> strlen($b['value']); // shortest
                    }
                    return strcmp($a['value'], $b['value']);          // alphabetical
                });
                $label = $candidates[0]['value'];
            }

            foreach ($members as $raw => $count) {
                $map[(string) $raw] = $label;
            }
        }

        return $map;
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  MANDATORY (PINNED) FACETS
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Resolve which mandatory-facet group applies, mirroring the spec blade's
     * getGroups(): subcategory name first, then category name, then default.
     * Matching is case-insensitive.
     *
     * @return array  [syntheticKey => [keywords...]]
     */
    private function resolveMandatoryGroup(?string $categoryName, ?string $subcategoryName): array
    {
        $byLower = [];
        foreach ($this->facetGroupsByCategory as $name => $def) {
            $byLower[strtolower(trim($name))] = $def;
        }

        $sub = strtolower(trim((string) $subcategoryName));
        $cat = strtolower(trim((string) $categoryName));

        if ($sub !== '' && isset($byLower[$sub])) return $byLower[$sub];
        if ($cat !== '' && isset($byLower[$cat])) return $byLower[$cat];
        return $this->facetGroupsByCategory['__default__'] ?? [];
    }

    /**
     * Score a raw attribute KEY against a keyword list (same idea as the spec
     * blade's scoreMatch): longer matches score higher, whole-word matches get
     * a bonus. Returns 0 for no match.
     */
    private function scoreKeyMatch(string $rawKey, array $keywords): int
    {
        $key   = strtolower(trim($rawKey));
        $score = 0;
        foreach ($keywords as $kw) {
            $kw = strtolower(trim($kw));
            if ($kw === '') continue;
            if (str_contains($key, $kw)) {
                $score += strlen($kw);
                // whole-word bonus
                if (preg_match('/\b' . preg_quote($kw, '/') . '\b/', $key)) {
                    $score += 10;
                }
            }
        }
        return $score;
    }

    /**
     * Build the mandatory-facet index for the scoped collection.
     *
     * MUST run AFTER primeBucketLabels() (it relies on resolveLabel()) and
     * BEFORE facetCounts()/topSections(). Sets $mandatoryFacetKeys and
     * $mandatoryFacetIndex. Idempotent per request.
     *
     * For each product, each custom attribute key is routed to AT MOST ONE
     * synthetic facet — whichever keyword group scores highest — so a single
     * "size" key can't double-count into two sections.
     */
    private function primeMandatoryFacets($products, ?string $categoryName, ?string $subcategoryName): void
    {
        $group = $this->resolveMandatoryGroup($categoryName, $subcategoryName);
        $this->mandatoryFacetKeys  = array_keys($group);
        $this->mandatoryFacetIndex = [];
        $this->absorbedAttributeKeys = [];

        if (empty($group)) return;

        foreach ($products as $p) {
            if (empty($p->custom_attributes) || !is_array($p->custom_attributes)) {
                continue;
            }

            foreach ($p->custom_attributes as $rawKey => $value) {
                if ($this->isExcludedAttribute($rawKey)) continue;

                // Which synthetic facet (if any) does this raw key belong to?
                $bestKey   = null;
                $bestScore = 0;
                foreach ($group as $synthKey => $keywords) {
                    $s = $this->scoreKeyMatch((string) $rawKey, $keywords);
                    if ($s > $bestScore) { $bestScore = $s; $bestKey = $synthKey; }
                }
                if ($bestKey === null) continue;

                // Record absorption so facetCounts() can skip this raw key
                // from the real-attribute path (prevents duplicate sections).
                $this->absorbedAttributeKeys[strtolower(trim((string) $rawKey))] = true;

                // Collapse each value to its display label and stash it.
                foreach ((array) $value as $val) {
                    $val = (string) $val;
                    if ($val === '') continue;
                    // resolveLabel uses the raw key's bucket map; this keeps the
                    // synthetic option labels consistent with the spec display.
                    $label = $this->resolveLabel((string) $rawKey, $val);
                    $this->mandatoryFacetIndex[$p->id][$bestKey][] = $label;
                }
            }

            // De-dupe labels per facet.
            if (isset($this->mandatoryFacetIndex[$p->id])) {
                foreach ($this->mandatoryFacetIndex[$p->id] as $k => $labels) {
                    $this->mandatoryFacetIndex[$p->id][$k] = array_values(array_unique($labels));
                }
            }
        }
    }

    /**
     * Is this synthetic key one of the request's mandatory (pinned) facets?
     */
    private function isMandatoryFacet(string $key): bool
    {
        return in_array($key, $this->mandatoryFacetKeys, true);
    }

    /**
     * The collapsed labels a product carries for a synthetic facet key.
     */
    private function productMandatoryValues($product, string $synthKey): array
    {
        return $this->mandatoryFacetIndex[$product->id][$synthKey] ?? [];
    }

    /**
     * Compute live facet counts for every axis.
     *
     * THE SMART BIT: for each axis we count only products that match every
     * OTHER currently-selected filter. So selecting "4K Ultra HD" makes the
     * HDR / Screen-type / Brand counts update to reflect only 4K products.
     * Counting an axis ignores that same axis's own selection, which is the
     * standard behaviour for multi-select facets (you can still widen within
     * a group you've already started selecting in).
     *
     * Returns:
     *   [
     *     'brands'       => [brand => count],
     *     'availability' => ['in-stock' => n, 'pre-order' => n],
     *     'categories'   => [categoryName => count],
     *     'custom'       => [key => [value => count]],
     *   ]
     *
     * Synthetic mandatory facets are counted into 'custom' too, so the blade
     * renders them exactly like any other custom facet.
     */
    private function facetCounts($products, Request $request): array
    {
        $facets = [
            'brands'       => [],
            'availability' => ['in-stock' => 0, 'pre-order' => 0],
            'categories'   => [],
            'custom'       => [],
        ];

        foreach ($products as $p) {

            // ── Brand axis: count against all OTHER filters except brand ──────
            if ($p->brand && $this->matchesFilters($p, $request, 'brands')) {
                $facets['brands'][$p->brand] = ($facets['brands'][$p->brand] ?? 0) + 1;
            }

            // ── Category axis: count against all OTHER filters except category ─
            if ($this->matchesFilters($p, $request, 'categories')) {
                $catName = $p->category->name ?? null;
                if ($catName) {
                    $facets['categories'][$catName] = ($facets['categories'][$catName] ?? 0) + 1;
                }
            }

            // ── Availability axis: count against all OTHER filters ────────────
            if ($this->matchesFilters($p, $request, 'availability')) {
                if (($p->stock ?? 0) > 0) {
                    $facets['availability']['in-stock']++;
                }
                if ($p->stock_status === 'pre_order') {
                    $facets['availability']['pre-order']++;
                }
            }

            // ── Custom attribute axes ─────────────────────────────────────────
            if (!empty($p->custom_attributes) && is_array($p->custom_attributes)) {
                foreach ($p->custom_attributes as $key => $value) {
                    if ($this->isExcludedAttribute($key)) continue;
                    // Skip keys absorbed into a mandatory synthetic facet —
                    // they are already counted below under the synthetic key.
                    if ($this->isAbsorbedAttribute($key)) continue;

                    // Count this attribute against all filters EXCEPT this key
                    if (!$this->matchesFilters($p, $request, 'options.' . $key)) {
                        continue;
                    }
                    foreach ((array) $value as $val) {
                        $val = (string) $val;
                        if ($val === '') continue;
                        $label = $this->resolveLabel($key, $val);   // collapse + alias
                        $facets['custom'][$key][$label] =
                            ($facets['custom'][$key][$label] ?? 0) + 1;
                    }
                }
            }

            // ── Mandatory (synthetic) facet axes ──────────────────────────────
            // Counted against all OTHER filters except this synthetic axis.
            foreach ($this->mandatoryFacetKeys as $synthKey) {
                if (!$this->matchesFilters($p, $request, 'options.' . $synthKey)) {
                    continue;
                }
                foreach ($this->productMandatoryValues($p, $synthKey) as $label) {
                    $facets['custom'][$synthKey][$label] =
                        ($facets['custom'][$synthKey][$label] ?? 0) + 1;
                }
            }
        }

        // Sort custom values alphabetically within each group for stable display
        foreach ($facets['custom'] as $key => $vals) {
            ksort($facets['custom'][$key]);
        }

        return $facets;
    }

    /**
     * Baseline (unfiltered) count of each canonical custom-attribute value
     * across the whole scoped collection — i.e. how many products in the
     * category have this value, ignoring any active filters.
     *
     * Used to decide which options are worth showing as filters. A value
     * that only ever appears on a single product is a one-off spec detail
     * (e.g. a unique dimension or HDMI port string), not a useful filter,
     * so it gets omitted. The threshold is checked against THIS baseline,
     * never the live faceted count, so options don't disappear as the user
     * narrows the selection.
     *
     * Synthetic mandatory facets are included so topSections() can score them.
     *
     * Returns: [key => [value => baselineCount]]
     */
    private function baselineCustomCounts($products): array
    {
        $counts = [];
        foreach ($products as $p) {
            if (!empty($p->custom_attributes) && is_array($p->custom_attributes)) {
                foreach ($p->custom_attributes as $key => $value) {
                    if ($this->isExcludedAttribute($key)) continue;
                    if ($this->isAbsorbedAttribute($key)) continue;
                    foreach ((array) $value as $val) {
                        $val = (string) $val;
                        if ($val === '') continue;
                        $label = $this->resolveLabel($key, $val);
                        $counts[$key][$label] = ($counts[$key][$label] ?? 0) + 1;
                    }
                }
            }

            // Synthetic mandatory facets.
            foreach ($this->mandatoryFacetKeys as $synthKey) {
                foreach ($this->productMandatoryValues($p, $synthKey) as $label) {
                    $counts[$synthKey][$label] = ($counts[$synthKey][$label] ?? 0) + 1;
                }
            }
        }
        return $counts;
    }

    /**
     * Pick the highest-count values to show as filter options for one
     * attribute, dropping the long tail of low-count values.
     *
     * RULE (per the catalogue's free-text reality): we don't want a sidebar
     * full of filters that each match only a product or two. So for each
     * attribute we keep only the top $limit values ranked by baseline count.
     * Given counts {1, 2, 5, 8} with $limit = 2, only 5 and 8 survive.
     *
     * Ties are broken alphabetically so the order is stable. Any value the
     * user has already selected is force-kept regardless of rank, so an
     * active filter is never dropped out from under them.
     *
     * @param  array $valueCounts     [value => count] for this attribute
     * @param  array $selectedValues  values currently ticked for this attribute
     * @param  int   $limit           how many top values to keep
     * @return array                  the kept values, alphabetically sorted
     */
    private function topValuesByCount(array $valueCounts, array $selectedValues, int $limit): array
    {
        // Rank by count DESC, then value ASC for stable tie-breaking.
        // Explicit comparator avoids version differences in sortBy()'s
        // multi-key array form, which silently mis-sorted here.
        $pairs = [];
        foreach ($valueCounts as $value => $count) {
            $pairs[] = ['value' => (string) $value, 'count' => (int) $count];
        }
        usort($pairs, function ($a, $b) {
            if ($a['count'] !== $b['count']) {
                return $b['count'] <=> $a['count']; // higher count first
            }
            return strcmp($a['value'], $b['value']);  // alphabetical tie-break
        });

        $ranked = array_slice(array_column($pairs, 'value'), 0, $limit);

        // Force-keep any selected value that the cut would have dropped, so an
        // active filter never vanishes. (This can push the list past $limit
        // while a filter is applied — that is intentional.)
        foreach ($selectedValues as $sel) {
            $sel = (string) $sel;
            if ($sel !== '' && isset($valueCounts[$sel]) && !in_array($sel, $ranked, true)) {
                $ranked[] = $sel;
            }
        }

        sort($ranked);
        return $ranked;
    }

    /**
     * Keep only the strongest attribute sections for the sidebar.
     *
     * Ranks each attribute by the total number of products covered by the
     * values being shown for it (summed baseline counts), and keeps the top
     * $max. An attribute the user currently has a filter applied to is always
     * kept regardless of rank. Mandatory (pinned) facets are always kept and
     * floated to the top of the sidebar.
     *
     * @param  array   $options   [key => [shownValues]]   (already value-capped)
     * @param  array   $baseline  [key => [value => baselineCount]]
     * @param  int     $max       max number of sections to keep
     * @return array   filtered [key => [shownValues]], strongest first
     */
    private function topSections(array $options, array $baseline, Request $request, int $max): array
    {
        $selectedKeys = array_keys((array) $request->input('options', []));

        // Score each section = sum of baseline counts of its shown values.
        $scored = [];
        foreach ($options as $key => $values) {
            $score = 0;
            foreach ($values as $v) {
                $score += $baseline[$key][$v] ?? 0;
            }
            $scored[$key] = $score;
        }

        // Sort by score desc, then key asc for stable ordering.
        uksort($scored, function ($a, $b) use ($scored) {
            if ($scored[$a] !== $scored[$b]) {
                return $scored[$b] <=> $scored[$a];
            }
            return strcmp($a, $b);
        });

        $keptKeys = array_slice(array_keys($scored), 0, $max);

        // Force-keep any attribute the user has an active filter on.
        foreach ($selectedKeys as $sk) {
            if (!in_array($sk, $keptKeys, true) && isset($options[$sk])) {
                $keptKeys[] = $sk;
            }
        }

        // Force-keep mandatory (pinned) facets regardless of score.
        foreach ($this->mandatoryFacetKeys as $mk) {
            if (!in_array($mk, $keptKeys, true) && isset($options[$mk])) {
                $keptKeys[] = $mk;
            }
        }

        // Order: mandatory facets first (in their declared order), then the
        // rest in ranked order.
        $mandatoryFirst = [];
        foreach ($this->mandatoryFacetKeys as $mk) {
            if (in_array($mk, $keptKeys, true)) {
                $mandatoryFirst[] = $mk;
            }
        }
        $rest = array_values(array_filter(
            $keptKeys,
            fn ($k) => !in_array($k, $mandatoryFirst, true)
        ));
        $keptKeys = array_merge($mandatoryFirst, $rest);

        // Rebuild in final order.
        $result = [];
        foreach ($keptKeys as $k) {
            if (isset($options[$k])) {
                $result[$k] = $options[$k];
            }
        }
        return $result;
    }

    /**
     * Remove any custom-attribute section whose values are really brand names,
     * so the Brands facet isn't duplicated by a brand-like custom attribute
     * (whatever it happens to be keyed as).
     *
     * Detection is by content, not key name: if most of a section's shown
     * values match an entry in the products' brand list, the section is a
     * brand duplicate and gets dropped. This is robust to the attribute being
     * named "brand", "manufacturer", "make", or anything else.
     *
     * @param  array $options  [key => [shownValues]]
     * @param  array $brands   the brand list shown in the dedicated Brands facet
     * @return array           options with brand-duplicate sections removed
     */
    private function dropBrandDuplicateSections(array $options, array $brands): array
    {
        if (empty($brands)) {
            return $options;
        }

        // Lowercased brand set for case-insensitive comparison.
        $brandSet = array_map(fn ($b) => strtolower(trim((string) $b)), $brands);

        foreach ($options as $key => $values) {
            if (empty($values)) continue;

            // Never treat a mandatory facet as a brand duplicate.
            if ($this->isMandatoryFacet($key)) continue;

            $matches = 0;
            foreach ($values as $v) {
                if (in_array(strtolower(trim((string) $v)), $brandSet, true)) {
                    $matches++;
                }
            }

            // If a majority of the section's values are brand names, treat the
            // whole section as a brand duplicate and drop it.
            if ($matches > 0 && $matches >= ceil(count($values) / 2)) {
                unset($options[$key]);
            }
        }

        return $options;
    }

    /**
     * Single source of truth: does $product satisfy the active filters?
     *
     * Used BOTH for faceting (with an ignored axis) and for the final
     * product filtering (with no ignored axis), so counts and results can
     * never disagree.
     *
     * @param  string|null $ignoreAxis  Axis to skip when checking — used by
     *         faceting so an axis doesn't constrain its own counts.
     *         Values: 'brands', 'availability', 'sizes', 'categories',
     *                 or 'options.<key>' for a specific custom/synthetic attribute.
     */
    private function matchesFilters($product, Request $request, ?string $ignoreAxis = null): bool
    {
        // ── Brand ───────────────────────────────────────────────────────────
        if ($ignoreAxis !== 'brands' && $request->filled('brands')) {
            $brands = array_filter((array) $request->input('brands'), fn ($b) => $b !== '');
            if (!empty($brands) && !in_array($product->brand, $brands)) {
                return false;
            }
        }

        // ── Availability ──────────────────────────────────────────────────────
        if ($ignoreAxis !== 'availability' && $request->filled('availability')) {
            $avail = array_filter(
                (array) $request->input('availability'),
                fn ($a) => in_array($a, ['in-stock', 'pre-order'])
            );
            if (!empty($avail)) {
                $ok = false;
                if (in_array('in-stock', $avail) && ($product->stock ?? 0) > 0)               $ok = true;
                if (in_array('pre-order', $avail) && $product->stock_status === 'pre_order')   $ok = true;
                if (!$ok) return false;
            }
        }

        // ── Sizes ─────────────────────────────────────────────────────────────
        if ($ignoreAxis !== 'sizes' && $request->filled('sizes')) {
            $sizes = array_filter((array) $request->input('sizes'), fn ($s) => $s !== '');
            if (!empty($sizes)) {
                $prodSizes = $product->sizes->pluck('name')->all();
                if (!array_intersect($sizes, $prodSizes)) return false;
            }
        }

        // ── Categories (search page only) ─────────────────────────────────────
        if ($ignoreAxis !== 'categories' && $request->filled('categories')) {
            $cats = array_filter((array) $request->input('categories'), fn ($c) => $c !== '');
            if (!empty($cats)) {
                $prodCat = $product->category->name ?? null;
                if (!$prodCat || !in_array($prodCat, $cats)) return false;
            }
        }

        // ── Custom attributes (real + synthetic mandatory) ───────────────────
        // Multi-select within a group = OR. Across groups = AND.
        if ($request->filled('options')) {
            $selected = (array) $request->input('options'); // [key => [values]]
            foreach ($selected as $key => $vals) {
                // Skip the axis we're currently counting (e.g. 'options.resolution')
                if ($ignoreAxis === 'options.' . $key) continue;

                $vals = array_filter((array) $vals, fn ($v) => $v !== '');
                if (empty($vals)) continue;

                if ($this->isMandatoryFacet($key)) {
                    // Synthetic facet: compare against the pre-collapsed labels
                    // indexed for this product (covers screen_size/size/etc.).
                    $prodVals = $this->productMandatoryValues($product, $key);
                } else {
                    // Real custom attribute: collapse the product's raw values
                    // to labels too, so a tick on "QLED" matches a product
                    // stored as "QLED (Quantum Dot LED) / VA".
                    $prodVals = array_map(
                        fn ($v) => $this->resolveLabel($key, (string) $v),
                        (array) ($product->custom_attributes[$key] ?? [])
                    );
                }

                if (!array_intersect($vals, $prodVals)) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Apply every filter (non-price + price) to the in-memory collection,
     * then sort. Returns a sorted, filtered, re-indexed collection.
     *
     * WHY PHP-SIDE:
     *   sell_price is a PHP accessor (markup + discount cascade), so price
     *   filtering and price sorting cannot be done in SQL. Since we are
     *   already in PHP for that, ALL filtering happens here against the
     *   shared matchesFilters() — keeping counts and results consistent.
     *
     * @param  string $sortBy  Usual values + 'relevance' (keep current order).
     */
    private function applyFiltersInPhp($products, Request $request, string $sortBy)
    {
        // ── Non-price filters via the shared matcher ──────────────────────────
        $filtered = $products->filter(fn ($p) => $this->matchesFilters($p, $request));

        // ── Price filter (sell_price accessor, NGN) ───────────────────────────
        $min = $request->input('min_price');
        $max = $request->input('max_price');
        $min = ($min !== null && $min !== '') ? (float) $min : null;
        $max = ($max !== null && $max !== '') ? (float) $max : null;

        if ($min !== null || $max !== null) {
            $filtered = $filtered->filter(function ($p) use ($min, $max) {
                $price = $p->sell_price;
                if ($min !== null && $price < $min) return false;
                if ($max !== null && $price > $max) return false;
                return true;
            });
        }

        // ── Sorting ───────────────────────────────────────────────────────────
        switch ($sortBy) {
            case 'price-asc':
                $filtered = $filtered->sortBy(fn ($p) => $p->sell_price);
                break;
            case 'price-desc':
                $filtered = $filtered->sortByDesc(fn ($p) => $p->sell_price);
                break;
            case 'newest':
                $filtered = $filtered->sortByDesc(fn ($p) => $p->created_at);
                break;
            case 'rating':
                $filtered = $filtered->sortByDesc(fn ($p) => $p->rating ?? 0);
                break;
            case 'relevance':
                // keep existing order (search relevance from SQL)
                break;
            case 'popularity':
            default:
                $filtered = $filtered->sortByDesc(fn ($p) => $p->id);
                break;
        }

        return $filtered->values();
    }

    /**
     * Manually paginate an in-memory collection (20 per page).
     */
    private function paginateCollection($collection, Request $request, int $perPage = 20): LengthAwarePaginator
    {
        $total       = $collection->count();
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $items       = $collection->slice(($currentPage - 1) * $perPage, $perPage)->values();

        return new LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );
    }
}