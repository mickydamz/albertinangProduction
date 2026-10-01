<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    // Only real columns are fillable. `content` is the review text (the storefront
    // product page reads/writes this); `comment`/`user_name` were phantom columns
    // that never existed on the table and have been removed.
    protected $fillable = [
        'product_id',
        'user_id',
        'supplier_id',
        'rating',
        'content',
        'image',
    ];

    protected $casts = [
        'rating' => 'integer',
    ];

    /**
     * Reviewer display name. There is no user_name column — derive it from the
     * author relationship so views that reference $review->user_name keep working.
     */
    public function getUserNameAttribute(): string
    {
        return $this->author?->name ?? 'Anonymous';
    }

    public function product()
    {
        return $this->belongsTo(Product::class,'product_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

 
    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    
    public function reviewImages()
    {
        return $this->hasMany(ReviewImage::class);
    }
}
