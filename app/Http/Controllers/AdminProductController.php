<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Image;
use App\Models\Tag;
use App\Models\Size;
use App\Models\Color;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminProductController extends Controller
{

public function __construct()
{
    $this->middleware(['auth', 'role:admin']);
}

    public function index(Request $request)
    {
        $search         = $request->get('search');
        $filterCategory = $request->get('category');
        $filterStatus   = $request->get('status');
        $filterBrand    = $request->get('brand');
        $sortBy         = $request->get('sort', 'created_at');
        $sortDir        = $request->get('dir', 'desc');

        // Whitelist sortable columns
        $allowedSorts = ['id', 'name', 'price', 'stock', 'created_at', 'is_active', 'rating'];
        if (!in_array($sortBy, $allowedSorts)) $sortBy = 'created_at';
        if (!in_array($sortDir, ['asc', 'desc'])) $sortDir = 'desc';

        $products = Product::with('Subcategory', 'category', 'images', 'tags', 'sizes', 'colors', 'locations', 'brand')
            ->withReviewStats()
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                      ->orWhere('description', 'like', '%' . $search . '%')
                      ->orWhereHas('category',    fn($q) => $q->where('name', 'like', '%'.$search.'%'))
                      ->orWhereHas('Subcategory', fn($q) => $q->where('name', 'like', '%'.$search.'%'))
                      ->orWhereHas('tags',        fn($q) => $q->where('name', 'like', '%'.$search.'%'))
                      ->orWhereHas('sizes',       fn($q) => $q->where('name', 'like', '%'.$search.'%'))
                      ->orWhereHas('colors',      fn($q) => $q->where('name', 'like', '%'.$search.'%'))
                      ->orWhereHas('locations',   fn($q) => $q->where('name', 'like', '%'.$search.'%'))
                      ->orWhereHas('brand',       fn($q) => $q->where('name', 'like', '%'.$search.'%'));
                });
            })
            ->when($filterCategory, fn($q) => $q->where('category_id', $filterCategory))
            ->when($filterBrand, function ($q) use ($filterBrand) {
                if (is_numeric($filterBrand)) {
                    $q->where('brand_id', $filterBrand);
                } else {
                    $q->whereHas('brand', fn($b) => $b->where('name', $filterBrand));
                }
            })
            ->when($filterStatus !== null && $filterStatus !== '',
                   fn($q) => $q->where('is_active', (bool) $filterStatus))
            ->orderBy($sortBy, $sortDir)
            ->paginate(10)
            ->withQueryString();

        $categories = Category::orderBy('name')->get();
        $brands     = Brand::orderBy('name')->get();

        return view('admin.products.index', compact(
            'products', 'search', 'filterCategory', 'filterStatus', 'filterBrand',
            'categories', 'brands', 'sortBy', 'sortDir'
        ));
    }

    public function create()
    {
        $categories = Category::with('subcategories')->get();
        $tags       = Tag::all();
        $sizes      = Size::all();
        $colors     = Color::all();
        $locations  = Location::all();
        $brands     = Brand::orderBy('name')->get();

        $product          = new Product();
        $customAttributes = [];
        $selectedTags     = [];

        return view('admin.products.create', compact(
            'categories', 'tags', 'sizes', 'colors', 'locations',
            'brands', 'product', 'customAttributes', 'selectedTags'
        ));
    }

public function store(Request $request)
{
    $request->validate([
        'name'                               => 'required|string|max:255',
        'description'                        => 'nullable|string',
        'brand_id'                           => 'nullable|exists:brands,id',
        'brand'                              => 'nullable|string',
        'price'                              => 'required|numeric',
        'subcategory_id'                     => 'nullable|exists:subcategories,id',
        'stock'                              => 'required|integer|min:0',
        'moq'                                => 'nullable|integer|min:1',
        'category_id'                        => 'required|exists:categories,id',
        'is_active'                          => 'boolean',
        'requires_truck'                     => 'nullable|boolean',
        'weight_kg'                          => 'nullable|numeric|min:0|max:99999',
        'images.*'                           => 'nullable|image|mimes:jpeg,png,jpg,gif|max:100048',
        'tags'                               => 'nullable|array',
        'tags.*'                             => 'string|max:255',
        'sizes'                              => 'nullable|array',
        'sizes.*'                            => 'exists:sizes,id',
        'colors'                             => 'nullable|array',
        'colors.*'                           => 'exists:colors,id',
        'locations'                          => 'nullable|array',
        'locations.*'                        => 'exists:locations,id',
        'options'                            => 'nullable|array',
        'markup_percent'                     => 'nullable|numeric|min:0|max:1000',
        'discount_percent'                   => 'nullable|numeric|min:0|max:100',
        'options.*.key'                      => 'required_with:options.*.value|string|max:255',
        'options.*.value'                    => 'nullable|string',
        'attr_groups'                        => 'nullable|array',
        'attr_groups.*.group'                => 'required_with:attr_groups|string|max:255',
        'attr_groups.*.attrs'                => 'nullable|array',
        'attr_groups.*.attrs.*.key'          => 'nullable|string|max:255',
        'attr_groups.*.attrs.*.value'        => 'nullable|string',
        'installation_options'               => 'nullable|array',
        'installation_options.*.label'       => 'nullable|string|max:255',
        'installation_options.*.price'       => 'nullable|numeric|min:0',
        'installation_options.*.description' => 'nullable|string|max:500',
    ]);

    $data = $request->only(
        'name', 'description', 'price',
        'subcategory_id', 'stock', 'category_id', 'moq'
    );

    $data['markup_percent'] = ($request->filled('markup_percent') && $request->markup_percent !== '0' && $request->markup_percent != 0)
        ? $request->markup_percent
        : null;

    $data['discount_percent'] = ($request->filled('discount_percent') && $request->discount_percent !== '0' && $request->discount_percent != 0)
        ? $request->discount_percent
        : null;

    // ── Active / Visible ─────────────────────────────────────────────────
    $data['is_active'] = $request->boolean('is_active');

    // ── Truck delivery (null = inherit from category) ─────────────────────
    $data['requires_truck'] = $request->filled('requires_truck_set')
        ? $request->boolean('requires_truck')
        : null;

    // ── Individual weight (null = inherit from subcategory/category) ───────
    $data['weight_kg'] = $request->filled('weight_kg') ? $request->weight_kg : null;

    // ── Resolve brand ────────────────────────────────────────────────────
    if ($request->filled('brand_id')) {
        $brand = Brand::find($request->brand_id);
        if ($brand) {
            $data['brand_id'] = $brand->id;
            $data['brand']    = $brand->name;
        }
    } elseif ($request->filled('brand')) {
        $brand = Brand::firstOrCreate(
            ['name' => $request->brand],
            ['slug' => Str::slug($request->brand)]
        );
        $data['brand_id'] = $brand->id;
        $data['brand']    = $brand->name;
    }

    // Ratings are review-driven, never set by hand. A brand-new product has no
    // reviews yet, so it starts unrated — ReviewController::updateProductRating()
    // fills these in automatically once customers review it.
    $data['rating']       = null;
    $data['rating_count'] = 0;

    // Manager is resolved automatically by the Product model's saving() hook,
    // which reads manager_id off the Brand record. No manual assignment here.
    $product = Product::create($data);

    // ── Images ───────────────────────────────────────────────────────────
    if ($request->hasFile('images')) {
        foreach ($request->file('images') as $image) {
            $imagePath = $image->store('images/products', 'public');
            $product->images()->create(['image_url' => $imagePath]);
        }
    }

    // ── Tags ─────────────────────────────────────────────────────────────
    $tagIdsToSync = [];
    if ($request->has('tags')) {
        foreach ($request->input('tags') as $tagInput) {
            if (is_numeric($tagInput)) {
                $tagIdsToSync[] = (int) $tagInput;
            } else {
                $tag = Tag::firstOrCreate(
                    ['slug' => Str::slug($tagInput)],
                    ['name' => $tagInput]
                );
                $tagIdsToSync[] = $tag->id;
            }
        }
    }
    $product->tags()->sync($tagIdsToSync);

    // ── Sizes / Colors / Locations ────────────────────────────────────────
    $product->sizes()->sync($request->input('sizes', []));
    $product->colors()->sync($request->input('colors', []));
    $product->locations()->sync($request->input('locations', []));

    // ── Custom Attributes + Grouped Attributes ────────────────────────────
    $flatAttributes = [];

    if ($request->has('options')) {
        foreach ($request->input('options') as $option) {
            $k = trim($option['key'] ?? '');
            $v = trim($option['value'] ?? '');
            if ($k !== '') {
                $flatAttributes[$k] = $v;
            }
        }
    }

    $groups = [];
    foreach ($request->input('attr_groups', []) as $group) {
        $groupName = trim($group['group'] ?? '');
        if (!$groupName) continue;

        $attrs = [];
        foreach ($group['attrs'] ?? [] as $attr) {
            $k = trim($attr['key'] ?? '');
            $v = trim($attr['value'] ?? '');
            if ($k !== '') {
                $attrs[]            = ['key' => $k, 'value' => $v];
                $flatAttributes[$k] = $v;
            }
        }

        $groups[] = ['group' => $groupName, 'attrs' => $attrs];
    }

    $product->custom_attributes       = $flatAttributes;
    $product->custom_attribute_groups = $groups;

    // ── Installation / Pricing Options ────────────────────────────────────
    $installationOptions = [];
    if ($request->has('installation_options')) {
        foreach ($request->input('installation_options') as $opt) {
            if (empty(trim($opt['label'] ?? '')) && empty(trim($opt['price'] ?? ''))) {
                continue;
            }
            if (!empty(trim($opt['label'] ?? ''))) {
                $installationOptions[] = [
                    'label'       => $opt['label'],
                    'price'       => (float) ($opt['price'] ?? 0),
                    'description' => $opt['description'] ?? '',
                ];
            }
        }
    }
    $product->installation_options = $installationOptions;

    // ── Description Blocks ────────────────────────────────────────────────
    if ($request->filled('description_blocks')) {
        $decoded = json_decode($request->input('description_blocks'), true);
        $product->description_blocks = is_array($decoded) ? $decoded : null;
    }

    $product->save();

    UserDashboardController::clearHomepageCache();

    return redirect()->route('admin.products.index')
        ->with('success', 'Product created successfully.');
}

    public function show($id)
    {
        $product = Product::with([
            'images', 'category', 'Subcategory', 'brand',
            'tags', 'sizes', 'colors', 'locations', 'reviews',
        ])->findOrFail($id);

        return view('admin.products.show', compact('product'));
    }

    public function edit($id)
    {
        $product    = Product::with('tags', 'sizes', 'colors', 'locations', 'brand')->withReviewStats()->findOrFail($id);
        $categories = Category::with('subcategories')->get();
        $tags       = Tag::all();
        $sizes      = Size::all();
        $colors     = Color::all();
        $locations  = Location::all();
        $brands     = Brand::orderBy('name')->get();

        $selectedTags = $product->tags->map(function ($tag) use ($product) {
            $customAttributes = is_array($product->custom_attributes)
                ? $product->custom_attributes
                : [];

            $tagOptions = isset($customAttributes['tag_options']) && is_array($customAttributes['tag_options'])
                ? $customAttributes['tag_options']
                : [];

            $customOptions = isset($tagOptions[$tag->name]) && is_array($tagOptions[$tag->name])
                ? array_filter($tagOptions[$tag->name], fn($opt) => !empty($opt))
                : [$tag->name];

            return [
                'id'            => $tag->id,
                'name'          => $tag->name,
                'isNew'         => false,
                'customOptions' => $customOptions,
            ];
        })->toArray();

        $customAttributes = is_array($product->custom_attributes)
            ? array_diff_key($product->custom_attributes, ['tag_options' => ''])
            : [];

        $installationOptions = is_array($product->installation_options)
            ? $product->installation_options
            : [];

        return view('admin.products.edit', compact(
            'product', 'categories', 'tags', 'sizes', 'colors', 'locations',
            'brands', 'selectedTags', 'customAttributes', 'installationOptions'
        ));
    }

public function update(Request $request, Product $product)
{
    $request->validate([
        'name'                               => 'required|string|max:255',
        'brand_id'                           => 'nullable|exists:brands,id',
        'brand'                              => 'nullable|string',
        'description'                        => 'nullable|string',
        'price'                              => 'required|numeric',
        'subcategory_id'                     => 'nullable|exists:subcategories,id',
        'stock'                              => 'required|integer|min:0',
        'moq'                                => 'nullable|integer|min:1',
        'markup_percent'                     => 'nullable|numeric|min:0|max:1000',
        'discount_percent'                   => 'nullable|numeric|min:0|max:100',
        'category_id'                        => 'required|exists:categories,id',
        'is_active'                          => 'boolean',
        'requires_truck'                     => 'nullable|boolean',
        'weight_kg'                          => 'nullable|numeric|min:0|max:99999',
        'removed_images'                     => 'nullable|string',
        'images.*'                           => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        'tags'                               => 'nullable|array',
        'tags.*'                             => 'string|max:255',
        'sizes'                              => 'nullable|array',
        'sizes.*'                            => 'exists:sizes,id',
        'colors'                             => 'nullable|array',
        'colors.*'                           => 'exists:colors,id',
        'locations'                          => 'nullable|array',
        'locations.*'                        => 'exists:locations,id',
        'options'                            => 'nullable|array',
        'options.*.key'                      => 'required_with:options.*.value|string|max:255',
        'options.*.value'                    => 'nullable|string',
        'attr_groups'                        => 'nullable|array',
        'attr_groups.*.group'                => 'required_with:attr_groups|string|max:255',
        'attr_groups.*.attrs'                => 'nullable|array',
        'attr_groups.*.attrs.*.key'          => 'nullable|string|max:255',
        'attr_groups.*.attrs.*.value'        => 'nullable|string',
        'installation_options'               => 'nullable|array',
        'installation_options.*.label'       => 'nullable|string|max:255',
        'installation_options.*.price'       => 'nullable|numeric|min:0',
        'installation_options.*.description' => 'nullable|string|max:500',
    ]);

    $data = $request->only(
        'name', 'description', 'price',
        'subcategory_id', 'stock', 'category_id', 'moq'
    );

    $data['markup_percent'] = ($request->filled('markup_percent') && $request->markup_percent !== '0' && $request->markup_percent != 0)
        ? $request->markup_percent
        : null;

    $data['discount_percent'] = ($request->filled('discount_percent') && $request->discount_percent !== '0' && $request->discount_percent != 0)
        ? $request->discount_percent
        : null;

    // ── Active / Visible ──────────────────────────────────────────────────
    $data['is_active'] = $request->boolean('is_active');

    // ── Truck delivery (null = inherit from category) ──────────────────────
    $data['requires_truck'] = $request->filled('requires_truck_set')
        ? $request->boolean('requires_truck')
        : null;

    // ── Individual weight (null = inherit from subcategory/category) ────────
    $data['weight_kg'] = $request->filled('weight_kg') ? $request->weight_kg : null;

    // ── Resolve brand ─────────────────────────────────────────────────────
    if ($request->filled('brand_id')) {
        $brand = Brand::find($request->brand_id);
        if ($brand) {
            $data['brand_id'] = $brand->id;
            $data['brand']    = $brand->name;
        }
    } elseif ($request->filled('brand')) {
        $brand = Brand::firstOrCreate(
            ['name' => $request->brand],
            ['slug' => Str::slug($request->brand)]
        );
        $data['brand_id'] = $brand->id;
        $data['brand']    = $brand->name;
    } else {
        $data['brand_id'] = null;
        $data['brand']    = null;
    }

    // Force the saving() hook to re-resolve the manager from the (possibly
    // changed) brand. Nulling it means the hook's empty-check passes and it
    // pulls the correct manager off the Brand record.
    $data['manager_id'] = null;

    $product->update($data);

    // ── Remove flagged images ──────────────────────────────────────────────
    if ($request->filled('removed_images')) {
        foreach (explode(',', $request->removed_images) as $imageId) {
            $image = $product->images()->find((int) $imageId);
            if ($image) {
                if (Storage::disk('public')->exists($image->image_url)) {
                    Storage::disk('public')->delete($image->image_url);
                }
                $image->delete();
            }
        }
    }

    // ── New image uploads ─────────────────────────────────────────────────
    if ($request->hasFile('images')) {
        foreach ($request->file('images') as $image) {
            $imagePath = $image->store('images/products', 'public');
            $product->images()->create(['image_url' => $imagePath]);
        }
    }

    // ── Tags ──────────────────────────────────────────────────────────────
    $tagIdsToSync = [];
    if ($request->has('tags')) {
        foreach ($request->input('tags') as $tagInput) {
            if (is_numeric($tagInput)) {
                $tagIdsToSync[] = (int) $tagInput;
            } else {
                $tag = Tag::firstOrCreate(
                    ['slug' => Str::slug($tagInput)],
                    ['name' => $tagInput]
                );
                $tagIdsToSync[] = $tag->id;
            }
        }
    }
    $product->tags()->sync($tagIdsToSync);

    // ── Sizes / Colors / Locations ────────────────────────────────────────
    $product->sizes()->sync($request->input('sizes', []));
    $product->colors()->sync($request->input('colors', []));
    $product->locations()->sync($request->input('locations', []));

    // ── Custom Attributes + Grouped Attributes ────────────────────────────
    $flatAttributes = [];

    if ($request->has('options')) {
        foreach ($request->input('options') as $option) {
            $k = trim($option['key'] ?? '');
            $v = trim($option['value'] ?? '');
            if ($k !== '') {
                $flatAttributes[$k] = $v;
            }
        }
    }

    $groups = [];
    foreach ($request->input('attr_groups', []) as $group) {
        $groupName = trim($group['group'] ?? '');
        if (!$groupName) continue;

        $attrs = [];
        foreach ($group['attrs'] ?? [] as $attr) {
            $k = trim($attr['key'] ?? '');
            $v = trim($attr['value'] ?? '');
            if ($k !== '') {
                $attrs[]            = ['key' => $k, 'value' => $v];
                $flatAttributes[$k] = $v;
            }
        }

        $groups[] = ['group' => $groupName, 'attrs' => $attrs];
    }

    $product->custom_attributes       = $flatAttributes;
    $product->custom_attribute_groups = $groups;

    // ── Installation / Pricing Options ────────────────────────────────────
    if ($request->has('installation_options')) {
        $installationOptions = [];
        foreach ($request->input('installation_options') as $opt) {
            if (empty(trim($opt['label'] ?? '')) && empty(trim($opt['price'] ?? ''))) {
                continue;
            }
            if (!empty(trim($opt['label'] ?? ''))) {
                $installationOptions[] = [
                    'label'       => $opt['label'],
                    'price'       => (float) ($opt['price'] ?? 0),
                    'description' => $opt['description'] ?? '',
                ];
            }
        }
        $product->installation_options = $installationOptions;
    }

    // ── Description Blocks ────────────────────────────────────────────────
    if ($request->filled('description_blocks')) {
        $decoded = json_decode($request->input('description_blocks'), true);
        $product->description_blocks = is_array($decoded) ? $decoded : null;
    } else {
        $product->description_blocks = null;
    }

    $product->save();

    UserDashboardController::clearHomepageCache();

    return redirect()->route('admin.products.index')
        ->with('success', 'Product updated successfully.');
}

    public function destroy(Product $product)
    {
        $product->tags()->detach();
        $product->sizes()->detach();
        $product->colors()->detach();
        $product->locations()->detach();

        foreach ($product->images as $image) {
            if (Storage::disk('public')->exists($image->image_url)) {
                Storage::disk('public')->delete($image->image_url);
            }
            $image->delete();
        }

        $product->delete();

        UserDashboardController::clearHomepageCache();

        return redirect()->route('admin.products.index')
            ->with('success', 'Product deleted successfully.');
    }

    public function removeImage($imageId)
    {
        $image = Image::findOrFail($imageId);

        if (Storage::disk('public')->exists($image->image_url)) {
            Storage::disk('public')->delete($image->image_url);
        }

        $image->delete();

        return response()->json(['success' => true, 'message' => 'Image removed successfully.']);
    }

    public function bulkMarkup(Request $request)
    {
        $request->validate([
            'markup_percent' => 'required|numeric|min:0|max:1000',
        ]);

        Product::query()->update([
            'markup_percent' => $request->markup_percent,
        ]);

        UserDashboardController::clearHomepageCache();

        return redirect()->route('admin.products.index')
            ->with('success', "Markup set to {$request->markup_percent}% on all products.");
    }

    public function toggleActive(Product $product)
    {
        $product->is_active = !$product->is_active;
        $product->save();

        UserDashboardController::clearHomepageCache();

        $status = $product->is_active ? 'activated' : 'deactivated';

        return redirect()->back()
            ->with('success', "Product \"{$product->name}\" has been {$status}.");
    }

public function uploadBlockImage(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
        ]);

        $file = $request->file('image');

        $jpeg = $this->resizeBlockImage(
            srcPath  : $file->getRealPath(),
            mime     : $file->getMimeType(),
            maxWidth : 1600,
            maxHeight: 1200,
            quality  : 82
        );

        $filename = 'block-' . uniqid() . '.jpg';
        $path     = 'images/blocks/' . $filename;

        Storage::disk('public')->put($path, $jpeg);

        return response()->json([
            'url' => asset('storage/' . $path),
        ]);
    }

    /**
     * Resize an image using GD, auto-rotate JPEG via EXIF, and return JPEG bytes.
     * Never upscales — if the image is already within bounds it is only re-encoded.
     */
    private function resizeBlockImage(
        string $srcPath,
        string $mime,
        int    $maxWidth,
        int    $maxHeight,
        int    $quality
    ): string {
        // ── Load source into GD ───────────────────────────────────────────
        $src = match (true) {
            str_contains($mime, 'png')  => imagecreatefrompng($srcPath),
            str_contains($mime, 'gif')  => imagecreatefromgif($srcPath),
            str_contains($mime, 'webp') => imagecreatefromwebp($srcPath),
            default                     => imagecreatefromjpeg($srcPath),
        };

        [$origW, $origH] = getimagesize($srcPath);

        // ── Auto-rotate JPEG based on EXIF (phone photos arrive sideways) ─
        if (str_contains($mime, 'jpeg') || str_contains($mime, 'jpg')) {
            $exif        = @exif_read_data($srcPath);
            $orientation = $exif['Orientation'] ?? 1;

            $src = match ($orientation) {
                3       => imagerotate($src, 180, 0),
                6       => imagerotate($src, -90, 0),
                8       => imagerotate($src,  90, 0),
                default => $src,
            };

            // After 90°/270° rotation the dimensions swap
            if (in_array($orientation, [5, 6, 7, 8])) {
                [$origW, $origH] = [$origH, $origW];
            }
        }

        // ── Calculate new size (preserve ratio, never upscale) ────────────
        $ratio = min(1.0, $maxWidth / $origW, $maxHeight / $origH);
        $newW  = (int) round($origW * $ratio);
        $newH  = (int) round($origH * $ratio);

        // ── Resample ──────────────────────────────────────────────────────
        $dst = imagecreatetruecolor($newW, $newH);

        // Fill white so transparent PNGs/GIFs look right after JPEG encode
        imagefill($dst, 0, 0, imagecolorallocate($dst, 255, 255, 255));
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $newW, $newH, $origW, $origH);
        imagedestroy($src);

        // ── Capture as JPEG bytes ─────────────────────────────────────────
        ob_start();
        imagejpeg($dst, null, $quality);
        $bytes = ob_get_clean();
        imagedestroy($dst);

        return $bytes;
    }
}