<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Category;
use App\Models\Color;
use App\Models\Location;
use App\Models\Size;
use App\Models\Subcategory;
use App\Models\Image;
use App\Models\Tag;
use App\Traits\Auditable;

class Product extends Model
{
    use HasFactory,Auditable;

    protected $fillable = [
        'name',
        'description',
        'description_blocks',
        'price',
        'stock',
        'image',
        'category_id',
        'subcategory_id',
        'rating',
        'rating_count',
        'moq',
        'location',
        'brand',
        'brand_id',
        'manager_id',
        'custom_attributes',
        'installation_options',
        'markup_percent',
        'discount_percent',
        'discount_expires_at',
        'installation_option',
        'installation_extra_ngn',
        'is_active',
        'requires_truck',
        'weight_kg',
        'custom_attribute_groups',
    ];

    protected $casts = [
        'custom_attributes'       => 'array',
        'custom_attribute_groups' => 'array',
        'installation_options'    => 'array',
        'description_blocks'      => 'array',
        'markup_percent'          => 'float',
        'discount_percent'        => 'float',
        'discount_expires_at'     => 'datetime',
        'installation_extra_ngn'  => 'integer',
        'is_active'               => 'boolean',
        'requires_truck'          => 'boolean',
        'weight_kg'               => 'float',
    ];

    protected array $auditExclude = ['password', 'remember_token', 'rating',];
    protected $appends = ['sell_price', 'old_price', 'requires_truck'];

    // ── Scopes ────────────────────────────────────────────────────────────────

public function getRequiresTruckAttribute(): bool
    {
        // 1. Product-level explicit override (null means inherit from category)
        $raw = $this->getRawOriginal('requires_truck') ?? $this->attributes['requires_truck'] ?? null;
        if (!is_null($raw) && $raw !== '') {
            return (bool) $raw;
        }

        // 2. Subcategory level
        if ($this->relationLoaded('Subcategory') && $this->Subcategory) {
            if ($this->Subcategory->requires_truck) {
                return true;
            }
        } elseif ($this->subcategory_id) {
            if (\App\Models\Subcategory::where('id', $this->subcategory_id)->value('requires_truck')) {
                return true;
            }
        }

        // 3. Category level
        if ($this->relationLoaded('category') && $this->category) {
            return (bool) $this->category->requires_truck;
        }

        if ($this->category_id) {
            return (bool) \App\Models\Category::where('id', $this->category_id)->value('requires_truck');
        }

        return false;
    }
    
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    // ── Relationships ─────────────────────────────────────────────────────────

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Eager-load the review aggregates every listing uses to draw stars, so
     * cards never trigger an N+1 and always match ProductController@show, which
     * counts real (non-null) review ratings rather than the denormalised
     * rating / rating_count columns. Add ->withReviewStats() to any product
     * query whose results render stars.
     */
    public function scopeWithReviewStats($query)
    {
        return $query
            ->withCount(['reviews as reviews_count' => fn ($q) => $q->whereNotNull('rating')])
            ->withAvg(['reviews as reviews_avg_rating' => fn ($q) => $q->whereNotNull('rating')], 'rating');
    }

    public function brand()
    {
        return $this->belongsTo(\App\Models\Brand::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function Subcategory()
    {
        return $this->belongsTo(Subcategory::class);
    }

    public function images()
    {
        return $this->hasMany(Image::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'product_tag', 'product_id', 'tag_id')
                    ->withPivot('selected_options');
    }

    public function sizes()
    {
        return $this->belongsToMany(Size::class, 'product_size', 'product_id', 'size_id');
    }

    public function colors()
    {
        return $this->belongsToMany(Color::class, 'product_color', 'product_id', 'color_id');
    }

    public function locations()
    {
        return $this->belongsToMany(Location::class, 'product_location', 'product_id', 'location_id');
    }

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function getAttributeGroups(): array
    {
        return is_array($this->custom_attribute_groups)
            ? $this->custom_attribute_groups
            : [];
    }

    // ── Accessors ─────────────────────────────────────────────────────────────

    /**
     * Number of real reviews — the single source of truth for "should we show
     * stars?", mirroring ProductController@show ($ratingCount = non-null review
     * count). Prefers a withReviewStats() aggregate, then an eager-loaded
     * relation, then a direct query as a last resort.
     */
    public function getReviewRatingCountAttribute(): int
    {
        if (array_key_exists('reviews_count', $this->attributes)) {
            return (int) $this->attributes['reviews_count'];
        }
        if ($this->relationLoaded('reviews')) {
            return $this->reviews->whereNotNull('rating')->count();
        }
        return (int) $this->reviews()->whereNotNull('rating')->count();
    }

    /**
     * Average rating drawn as stars — 0 when there are no real reviews, exactly
     * like ProductController@show ($displayRating = $count > 0 ? avg : 0). The
     * denormalised rating column is ignored so every page stays in agreement.
     */
    public function getReviewRatingAttribute(): float
    {
        if ($this->review_rating_count === 0) {
            return 0.0;
        }
        if (array_key_exists('reviews_avg_rating', $this->attributes)) {
            return round((float) $this->attributes['reviews_avg_rating'], 1);
        }
        if ($this->relationLoaded('reviews')) {
            return round((float) $this->reviews->whereNotNull('rating')->avg('rating'), 1);
        }
        return round((float) $this->reviews()->whereNotNull('rating')->avg('rating'), 1);
    }

    public function getSellPriceAttribute(): float
    {
        $markedUp = $this->resolveMarkedUpPrice((float) ($this->attributes['price'] ?? 0));
        $discount = $this->resolveActiveDiscount();
        return $discount ? $markedUp * (1 - $discount / 100) : $markedUp;
    }

    public function getOldPriceAttribute(): ?float
    {
        $markedUp = $this->resolveMarkedUpPrice((float) ($this->attributes['price'] ?? 0));
        return $this->resolveActiveDiscount() ? $markedUp : null;
    }

    // ── Private helpers ───────────────────────────────────────────────────────

    /**
     * Walks product → subcategory → category and returns the first
     * discount_percent that exists AND has not yet expired.
     */
    private function resolveActiveDiscount(): ?float
    {
        $now = now();

        // Product-level
        $d = $this->attributes['discount_percent'] ?? null;
        $e = $this->attributes['discount_expires_at'] ?? null;
        if (!is_null($d) && ($e === null || $now->lt(\Carbon\Carbon::parse($e)))) {
            return (float) $d;
        }

        // Subcategory-level
        if ($this->subcategory_id) {
            $sub = $this->relationLoaded('Subcategory') && $this->Subcategory
                ? $this->Subcategory
                : \App\Models\Subcategory::where('id', $this->subcategory_id)
                    ->select('discount_percent', 'discount_expires_at')->first();

            if ($sub && !is_null($sub->discount_percent)) {
                if (is_null($sub->discount_expires_at) || $now->lt($sub->discount_expires_at)) {
                    return (float) $sub->discount_percent;
                }
            }
        }

        // Category-level
        if ($this->category_id) {
            $cat = $this->relationLoaded('category') && $this->category
                ? $this->category
                : \App\Models\Category::where('id', $this->category_id)
                    ->select('discount_percent', 'discount_expires_at')->first();

            if ($cat && !is_null($cat->discount_percent)) {
                if (is_null($cat->discount_expires_at) || $now->lt($cat->discount_expires_at)) {
                    return (float) $cat->discount_percent;
                }
            }
        }

        return null;
    }

    private function resolveMarkedUpPrice(float $basePrice): float
    {
        $productMarkup = $this->attributes['markup_percent'] ?? null;
        if (!is_null($productMarkup)) {
            return $basePrice * (1 + (float) $productMarkup / 100);
        }

        if ($this->subcategory_id) {
            $subMarkup = $this->relationLoaded('Subcategory') && $this->Subcategory
                ? $this->Subcategory->markup_percent
                : \App\Models\Subcategory::where('id', $this->subcategory_id)->value('markup_percent');
            if (!is_null($subMarkup)) {
                return $basePrice * (1 + (float) $subMarkup / 100);
            }
        }

        if ($this->category_id) {
            $catMarkup = $this->relationLoaded('category') && $this->category
                ? $this->category->markup_percent
                : \App\Models\Category::where('id', $this->category_id)->value('markup_percent');
            if (!is_null($catMarkup)) {
                return $basePrice * (1 + (float) $catMarkup / 100);
            }
        }

        return $basePrice;
    }

    // ── Custom Attribute Helpers ──────────────────────────────────────────────

    public function custom(string $key, mixed $default = null): mixed
    {
        return (is_array($this->custom_attributes) ? $this->custom_attributes[$key] : null) ?? $default;
    }

    public function setCustom(string $key, mixed $value): void
    {
        $attributes = $this->custom_attributes ?? [];
        if (!is_array($attributes)) {
            $attributes = [];
        }
        $attributes[$key] = $value;
        $this->custom_attributes = $attributes;
    }

    public function getAllCustomAttributes(): array
    {
        return is_array($this->custom_attributes) ? $this->custom_attributes : [];
    }


    protected static function booted(): void
{
    static::saving(function (Product $product) {
        // Don't override an explicit manual assignment
        if (!empty($product->manager_id)) {
            return;
        }

        // No brand at all — nothing to look up
        if (empty($product->brand_id) && empty($product->brand)) {
            return;
        }

        $managerId = null;

        if ($product->brand_id) {
            $managerId = \App\Models\Brand::whereKey($product->brand_id)->value('manager_id');
        }

        if (is_null($managerId) && !empty($product->brand)) {
            $managerId = \App\Models\Brand::whereRaw('TRIM(name) = ?', [trim($product->brand)])
                ->value('manager_id');
        }

        if (!is_null($managerId)) {
            $product->manager_id = $managerId;
        }
    });
}
}