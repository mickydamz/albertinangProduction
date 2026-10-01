<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'name',
        'price',
        'quantity',
        'sku',
        'image',
        // ⚠️ DO NOT REMOVE. Without these in $fillable, mass-assignment silently
        //    drops installation data when order items are created, even if the
        //    controllers/services pass it correctly.
        'installation_option',
        'installation_extra_ngn',
    ];

    protected $casts = [
        'price'                  => 'decimal:2',
        'quantity'               => 'integer',
        'product_id'             => 'integer',
        'installation_extra_ngn' => 'integer',
        'created_at'             => 'datetime',
        'updated_at'             => 'datetime',
    ];

    protected $appends = ['image_url'];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Proper Eloquent relationship — use this in queries/whereHas.
     */
    public function productRelation(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /**
     * Kept for image resolution and anywhere product() was used before.
     * Now checks product_id first, then falls back to sku/name.
     */
    public function product(): ?Product
    {
        // 1. Use product_id (fast, reliable — new way)
        if ($this->product_id) {
            $product = Product::find($this->product_id);
            if ($product) return $product;
        }

        // 2. Fall back to SKU (old orders without product_id)
        if ($this->sku) {
            $product = Product::where('sku', $this->sku)->first();
            if ($product) return $product;
        }

        // 3. Last resort: match by name
        if ($this->name) {
            return Product::where('name', $this->name)->first();
        }

        return null;
    }

    public function getImageUrlAttribute(): string
    {
        return $this->getImage();
    }

    public function getImage(): string
    {
        $placeholder = 'https://placehold.co/56x56/eef3e8/3d8012?text=No+Image';

        // 1. Try the stored image column first
        if ($this->image) {
            $resolved = $this->resolveImageUrl($this->image);
            if ($resolved !== $placeholder) return $resolved;
        }

        // 2. Fall back to the live product record
        $product = $this->product();

        if ($product) {
            if ($product->image) {
                $resolved = $this->resolveImageUrl($product->image);
                if ($resolved !== $placeholder) return $resolved;
            }

            $firstImage = $product->images()->first();
            if ($firstImage?->image_url) {
                $resolved = $this->resolveImageUrl($firstImage->image_url);
                if ($resolved !== $placeholder) return $resolved;
            }
        }

        return $placeholder;
    }

    protected function resolveImageUrl(?string $path): string
    {
        $placeholder = 'https://placehold.co/56x56/eef3e8/3d8012?text=No+Image';

        if (!$path) return $placeholder;

        // Already a full URL — strip localhost ones down to their path
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            if (preg_match('#https?://localhost[^/]*/storage/(.+)$#', $path, $m) ||
                preg_match('#https?://127\.0\.0\.1[^/]*/storage/(.+)$#', $path, $m)) {
                $path = $m[1];
            } else {
                return $path;
            }
        }

        $clean = ltrim($path, '/');
        $clean = preg_replace('#^storage/#', '', $clean);

        if (Storage::disk('public')->exists($clean)) {
            return Storage::disk('public')->url($clean);
        }

        if (file_exists(public_path($clean))) {
            return asset($clean);
        }

        if (file_exists(public_path('storage/' . $clean))) {
            return asset('storage/' . $clean);
        }

        return asset('storage/' . $clean);
    }
}