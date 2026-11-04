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

    public function create()
    {
        $brands = \App\Models\Brand::where('manager_id', auth()->id())->orderBy('name')->get();
        $categories = \App\Models\Category::with('subcategories')->get();
        $tags = \App\Models\Tag::all(); $sizes = \App\Models\Size::all();
        $colors = \App\Models\Color::all(); $locations = \App\Models\Location::all();
        $product = new Product(); $customAttributes = []; $selectedTags = [];
        $previousOptions = [];
        foreach (old('new_tag_options_data', []) as $json) {
            $entry = json_decode($json, true);
            if (is_array($entry) && isset($entry['name']) && is_array($entry['options'] ?? null)) $previousOptions[$entry['name']] = $entry['options'];
        }
        foreach (old('tags', []) as $input) {
            $tag = ctype_digit((string)$input) ? \App\Models\Tag::find($input) : null;
            if (!$tag && ctype_digit((string)$input)) continue;
            $name = $tag ? $tag->name : strip_tags((string)$input);
            $selectedTags[] = ['id'=>$tag?->id, 'name'=>$name, 'isNew'=>!$tag, 'customOptions'=>$previousOptions[$name] ?? [$name]];
        }
        return view('manager.products.create', compact('brands','categories','tags','sizes','colors','locations','product','customAttributes','selectedTags'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'brand_id' => ['required', 'integer', \Illuminate\Validation\Rule::exists('brands', 'id')->where('manager_id', auth()->id())],
            'name' => 'required|string|max:255', 'description' => 'nullable|string|max:10000',
            'category_id' => 'required|integer|exists:categories,id',
            'subcategory_id' => [\Illuminate\Validation\Rule::requiredIf(fn () => \App\Models\Subcategory::where('category_id', $request->input('category_id'))->exists()), 'nullable', 'integer', \Illuminate\Validation\Rule::exists('subcategories', 'id')->where('category_id', $request->input('category_id'))],
            'price' => 'required|numeric|min:0.01|max:999999999',
            'stock' => 'required|integer|min:0|max:1000000', 'moq' => 'nullable|integer|min:1|max:1000000',
            'weight_kg' => 'nullable|numeric|min:0|max:99999',
            'images' => 'nullable|array|max:10', 'images.*' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
            'manager_id' => 'prohibited', 'supplier_id' => 'prohibited', 'brand' => 'prohibited',
                        'is_active' => 'prohibited',
            'requires_truck_set' => 'nullable|in:1', 'requires_truck' => 'nullable|boolean',
            'markup_percent' => 'nullable|numeric|min:0|max:1000', 'discount_percent' => 'nullable|numeric|min:0|max:100',
            'tags' => 'nullable|array|max:50', 'tags.*' => 'required|string|max:255',
            'new_tag_options_data' => 'nullable|array|max:50', 'new_tag_options_data.*' => 'required|json|max:10000',
            'sizes' => 'nullable|array|max:100', 'sizes.*' => 'integer|exists:sizes,id',
            'colors' => 'nullable|array|max:100', 'colors.*' => 'integer|exists:colors,id',
            'pickup_points'=>'nullable|array|max:100', 'pickup_points.*'=>'integer|exists:pickup_points,id',
            'locations' => 'nullable|array|max:100', 'locations.*' => 'integer|exists:locations,id',
            'options' => 'nullable|array|max:200', 'options.*.key' => 'required|string|max:255', 'options.*.value' => 'nullable|string|max:2000',
            'attr_groups' => 'nullable|array|max:50', 'attr_groups.*.group' => 'required|string|max:255',
            'attr_groups.*.attrs' => 'nullable|array|max:200', 'attr_groups.*.attrs.*.key' => 'nullable|string|max:255',
            'attr_groups.*.attrs.*.value' => 'nullable|string|max:2000',
            'installation_options' => 'nullable|array|max:30', 'installation_options.*.label' => 'nullable|string|max:255',
            'installation_options.*.price' => 'nullable|numeric|min:0|max:999999999', 'installation_options.*.description' => 'nullable|string|max:500',
            'description_blocks' => 'nullable|json|max:200000',
        ]);
        $paths = [];
        try {
            $product = \Illuminate\Support\Facades\DB::transaction(function () use ($data, $request, &$paths) {
                // Recheck ownership under a lock so a concurrent reassignment cannot grant access.
                $brand = \App\Models\Brand::where('manager_id', auth()->id())->lockForUpdate()->findOrFail($data['brand_id']);
                $product = Product::create([
                    'name' => $data['name'], 'description' => strip_tags($data['description'] ?? ''),
                    'brand_id' => $brand->id, 'brand' => $brand->name, 'manager_id' => auth()->id(),
                    'category_id' => $data['category_id'], 'subcategory_id' => $data['subcategory_id'] ?? null,
                    'stock' => $data['stock'], 'moq' => $data['moq'] ?? 1, 'weight_kg' => $data['weight_kg'] ?? null,
                    'price' => $data['price'], 'is_active' => false,
                    'requires_truck' => $request->filled('requires_truck_set') ? $request->boolean('requires_truck') : null,
                    'markup_percent' => !empty($data['markup_percent']) ? $data['markup_percent'] : null,
                    'discount_percent' => !empty($data['discount_percent']) ? $data['discount_percent'] : null,
                ]);
                foreach ($request->file('images', []) as $image) {
                    $path = $image->store('images/products', 'public'); $paths[] = $path;
                    $product->images()->create(['image_url' => $path]);
                }
                $flat = []; $groups = [];
                foreach ($data['options'] ?? [] as $option) {
                    $key = trim($option['key']); if ($key !== '') $flat[$key] = trim($option['value'] ?? '');
                }
                foreach ($data['attr_groups'] ?? [] as $group) {
                    $attributes = [];
                    foreach ($group['attrs'] ?? [] as $attr) {
                        $key = trim($attr['key'] ?? ''); if ($key === '') continue;
                        $value = trim($attr['value'] ?? ''); $attributes[] = ['key'=>$key,'value'=>$value]; $flat[$key] = $value;
                    }
                    $groups[] = ['group'=>trim($group['group']),'attrs'=>$attributes];
                }
                $tagIds = []; $tagNames = [];
                foreach ($data['tags'] ?? [] as $input) {
                    if (ctype_digit((string)$input)) $tag = \App\Models\Tag::findOrFail($input);
                    else {
                        $name = trim(strip_tags($input)); $slug = \Illuminate\Support\Str::slug($name);
                        if ($slug === '') throw \Illuminate\Validation\ValidationException::withMessages(['tags'=>'Enter a valid tag name.']);
                        $tag = \App\Models\Tag::firstOrCreate(['slug'=>$slug],['name'=>$name]);
                    }
                    $tagIds[] = $tag->id; $tagNames[$tag->name] = true;
                }
                $tagOptions = [];
                foreach ($data['new_tag_options_data'] ?? [] as $json) {
                    $entry = json_decode($json, true);
                    if (!is_array($entry) || !isset($tagNames[$entry['name'] ?? '']) || !is_array($entry['options'] ?? null))
                        throw \Illuminate\Validation\ValidationException::withMessages(['tags'=>'Tag options must belong to a selected tag.']);
                    foreach ($entry['options'] as $value) if (!is_string($value) || strlen($value)>255)
                        throw \Illuminate\Validation\ValidationException::withMessages(['tags'=>'Invalid tag option.']);
                    $tagOptions[$entry['name']] = array_values($entry['options']);
                }
                if ($tagOptions) $flat['tag_options'] = $tagOptions;
                $product->tags()->sync(array_unique($tagIds));
                foreach (['sizes','colors','locations'] as $relation) $product->$relation()->sync($data[$relation] ?? []);
                $product->pickupPoints()->sync(\App\Services\ShoppingLocation::validateAssignments($data['locations']??[],$data['pickup_points']??[],false));
                $installation = [];
                foreach ($data['installation_options'] ?? [] as $option) {
                    if (trim($option['label'] ?? '') === '') continue;
                    $installation[] = ['label'=>$option['label'],'price'=>(float)($option['price'] ?? 0),'description'=>$option['description'] ?? ''];
                }
                $blocks = empty($data['description_blocks']) ? null : json_decode($data['description_blocks'], true);
                if ($blocks !== null && (!is_array($blocks) || !array_is_list($blocks)))
                    throw \Illuminate\Validation\ValidationException::withMessages(['description_blocks'=>'Description blocks must be a list.']);
                $this->validateDescriptionBlocks($blocks ?? []);
                $product->update(['custom_attributes'=>$flat,'custom_attribute_groups'=>$groups,'installation_options'=>$installation,'description_blocks'=>$blocks]);
                return $product;
            });
        } catch (\Throwable $error) {
            foreach ($paths as $path) Storage::disk('public')->delete($path);
            throw $error;
        }
        \App\Http\Controllers\UserDashboardController::clearHomepageCache();
        return redirect()->route('manager.products.edit', $product)->with('success', $product->is_active ? 'Product published with all product details.' : 'Product saved as a draft with all product details.');
    }

    private function validateDescriptionBlocks(array $nodes, int $depth = 0): void
    {
        if ($depth > 20) throw \Illuminate\Validation\ValidationException::withMessages(['description_blocks'=>'Description nesting is too deep.']);
        foreach ($nodes as $key => $value) {
            if (is_array($value)) $this->validateDescriptionBlocks($value, $depth+1);
            elseif ($key === 'url' && is_string($value) && $value !== '' && !preg_match('~^(https?://|/(?!/)|\#)~i', $value))
                throw \Illuminate\Validation\ValidationException::withMessages(['description_blocks'=>'Use an HTTP(S) or local URL for description links and images.']);
        }
    }

    public function uploadBlockImage(Request $request): \Illuminate\Http\JsonResponse
    {
        abort_unless(\App\Models\Brand::where('manager_id', auth()->id())->exists(), 403);
        return (new AdminProductController())->uploadBlockImage($request);
    }

    public function toggleActive(Product $product)
    {
        abort(403, 'Only an administrator can publish or unpublish products.');
    }

    // =========================================================================
    //  Edit
    // =========================================================================

    public function edit(Product $product)
    {
        $product = $this->findOwned($product->id);
        $submission = \App\Models\CatalogueRevision::where('editor_id',auth()->id())->where('product_id',$product->id)->latest('id')->first();
        return view('manager.products.edit', compact('product','submission'));
    }

    // =========================================================================
    //  Update
    // =========================================================================

    public function update(Request $request, Product $product)
    {
        $product = $this->findOwned($product->id);

        $validated = $request->validate([
            'is_active' => 'prohibited', 'manager_id' => 'prohibited', 'brand_id' => 'prohibited',
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
