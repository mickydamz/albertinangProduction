<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ManagerProductController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'manager']);
    }

    // =========================================================================
    //  Helpers — ownership
    // =========================================================================

    /**
     * Base query scoped to the authenticated manager's products only.
     */
    private function ownedProducts()
    {
        return Product::where('manager_id', auth()->id());
    }

    /**
     * Fetch a product, 404 if it doesn't belong to the manager.
     */
    private function findOwned(int $id): Product
    {
        return $this->ownedProducts()->findOrFail($id);
    }

    // =========================================================================
    //  Helpers — smart NAME-ONLY search
    // =========================================================================

    /**
     * Synonym groups for words that are spelled completely differently
     * but mean the same product. The automatic stemming below already
     * handles plurals/variants (fan/fans, freezer/freezers), so ONLY
     * add genuinely different words here.
     *
     * Covers the whole catalogue — add a line any time you notice a
     * search term managers use that the product name doesn't contain.
     */
    private array $searchSynonyms = [
        // Sound & Vision
        ['tv', 'television'],
        ['speaker', 'soundbar', 'sound bar'],
        ['headphone', 'headset', 'earphone', 'earbud'],
        ['hometheater', 'home theater', 'home theatre'],

        // Air cooling
        ['ac', 'air conditioner', 'airconditioner', 'aircon', 'split unit'],
        ['fan', 'ventilator'],

        // Kitchen
        ['fridge', 'refrigerator'],
        ['freezer', 'deep freezer', 'chest freezer'],
        ['cooker', 'stove', 'gas cooker', 'range'],
        ['microwave', 'micro wave'],
        ['blender', 'smoothie maker', 'liquidiser', 'liquidizer'],
        ['kettle', 'jug kettle', 'electric jug'],

        // Garment care
        ['washer', 'washing machine', 'laundry machine'],
        ['iron', 'pressing iron', 'steam iron'],
        ['dryer', 'drying machine', 'tumble dryer'],

        // Power
        ['generator', 'gen', 'genset'],
        ['inverter', 'power inverter'],
        ['stabilizer', 'stabiliser', 'avr', 'voltage regulator'],
    ];

    /**
     * Reduce a word to a crude stem so plural/variant forms match:
     * televisions → television, freezers → freezer, dishes → dish.
     * Generic — works for every product name, no list required.
     */
    private function stem(string $word): string
    {
        $w = strtolower(trim($word));
        if (strlen($w) > 4 && str_ends_with($w, 'es')) {
            return substr($w, 0, -2);
        }
        if (strlen($w) > 3 && str_ends_with($w, 's')) {
            return substr($w, 0, -1);
        }
        return $w;
    }

    /**
     * Expand one typed word into every variant that should match:
     * the word itself, its stem, and every member (plus stem) of any
     * synonym group it belongs to.
     */
    private function expandTerm(string $term): array
    {
        $term     = strtolower(trim($term));
        $stem     = $this->stem($term);
        $variants = [$term, $stem];

        foreach ($this->searchSynonyms as $group) {
            // Match the group on raw OR stemmed form, so "tvs" still
            // lands in the tv/television group.
            $groupStems = array_map(fn ($g) => $this->stem($g), $group);
            if (in_array($term, $group, true) || in_array($stem, $groupStems, true)) {
                foreach ($group as $g) {
                    $variants[] = $g;
                    $variants[] = $this->stem($g);
                }
            }
        }

        // De-dupe, drop empties / single-char fragments that would match everything
        return array_values(array_unique(array_filter(
            $variants,
            fn ($v) => $v !== '' && strlen($v) >= 2
        )));
    }

    /**
     * Search PRODUCT NAME ONLY. Every typed word must appear in the
     * name — directly, as a stem, or via a synonym. No category /
     * description / brand fallback. Nothing close → the view shows
     * "No products match your search/filters."
     */
    private function applySearch($query, ?string $search)
    {
        $terms = preg_split('/\s+/', trim((string) $search), -1, PREG_SPLIT_NO_EMPTY);
        $terms = array_slice($terms, 0, 5); // cap to keep the query sane

        if (empty($terms)) {
            return $query;
        }

        return $query->where(function ($outer) use ($terms) {
            foreach ($terms as $term) {
                $variants = $this->expandTerm($term);
                // This word (or one of its variants) must be in the name
                $outer->where(function ($q) use ($variants) {
                    foreach ($variants as $v) {
                        $q->orWhere('name', 'like', '%' . $v . '%');
                    }
                });
            }
        });
    }

    // =========================================================================
    //  Index — full product list with brand tabs, search, filters, sorting
    // =========================================================================

    public function index(Request $request)
    {
        $search       = $request->get('search');
        $filterStatus = $request->get('status');   // is_active: '1' / '0' / null
        $filterStock  = $request->get('stock');    // 'in' / 'out' / null
        $sortBy       = $request->get('sort', 'created_at');
        $sortDir      = $request->get('dir', 'desc');

        // Whitelist sortable columns
        $allowedSorts = ['id', 'name', 'price', 'stock', 'created_at', 'is_active', 'rating'];
        if (!in_array($sortBy, $allowedSorts))    $sortBy  = 'created_at';
        if (!in_array($sortDir, ['asc', 'desc'])) $sortDir = 'desc';

        // Stats across ALL owned products (unaffected by filters)
        $totalProducts = $this->ownedProducts()->count();
        $totalBrands   = $this->ownedProducts()->whereNotNull('brand')->distinct('brand')->count('brand');
        $inStock       = $this->ownedProducts()->where('stock', '>', 0)->count();
        $outOfStock    = $this->ownedProducts()->where('stock', '<=', 0)->count();

        // Brands (with per-brand counts) for the tabs
        $brands = $this->ownedProducts()
            ->whereNotNull('brand')
            ->where('brand', '!=', '')
            ->selectRaw('brand, COUNT(*) as total')
            ->groupBy('brand')
            ->orderBy('brand')
            ->get();

        // Active tab (null = "All"); validate against the manager's brand NAMES
        $activeBrand = $request->query('brand');
        if ($activeBrand !== null && !$brands->pluck('brand')->contains($activeBrand)) {
            $activeBrand = null;
        }

        // Shared query builder so the paginated list and recent list match
        $buildQuery = function () use ($activeBrand, $search, $filterStatus, $filterStock) {
            $query = $this->ownedProducts()
                ->with('images')
                ->when($activeBrand, fn ($q) => $q->where('brand', $activeBrand));

            $query = $this->applySearch($query, $search);

            return $query
                ->when($filterStatus !== null && $filterStatus !== '',
                       fn ($q) => $q->where('is_active', (bool) $filterStatus))
                ->when($filterStock === 'in',  fn ($q) => $q->where('stock', '>', 0))
                ->when($filterStock === 'out', fn ($q) => $q->where('stock', '<=', 0));
        };

        // When searching, rank name-prefix matches first, then name-contains,
        // before falling back to the chosen column sort.
        $products = $buildQuery()
            ->when($search, function ($q) use ($search) {
                $s = trim($search);
                $q->orderByRaw("
                    CASE
                        WHEN name LIKE ? THEN 0
                        WHEN name LIKE ? THEN 1
                        ELSE 2
                    END
                ", [$s . '%', '%' . $s . '%']);
            })
            ->orderBy($sortBy, $sortDir)
            ->paginate(15)
            ->withQueryString();

        $recentProducts = $buildQuery()
            ->latest()
            ->take(5)
            ->get();

        return view('manager.dashboard', compact(
            'totalProducts', 'totalBrands', 'inStock', 'outOfStock',
            'brands', 'activeBrand', 'products', 'recentProducts',
            'search', 'filterStatus', 'filterStock', 'sortBy', 'sortDir'
        ));
    }

    // =========================================================================
    //  Toggle Active (AJAX) — same behaviour as the admin side
    // =========================================================================

    public function toggleActive(Product $product)
    {
        // 404 if the product doesn't belong to this manager
        $product = $this->findOwned($product->id);

        $product->is_active = ! $product->is_active;
        $product->save();

        return response()->json([
            'success'   => true,
            'is_active' => (bool) $product->is_active,
        ]);
    }

    // =========================================================================
    //  Edit
    // =========================================================================

    public function edit(Product $product)
    {
        $product = $this->findOwned($product->id);

        return view('manager.products.edit', compact('product'));
    }

    // =========================================================================
    //  Update
    // =========================================================================

    public function update(Request $request, Product $product)
    {
        $product = $this->findOwned($product->id);

        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'price'       => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::delete('public/' . $product->image);
            }
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($validated);

        return redirect()->route('manager.products.index')
                         ->with('success', 'Product updated successfully.');
    }
}