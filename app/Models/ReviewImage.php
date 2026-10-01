<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReviewImage extends Model
{
    use HasFactory;

    protected $fillable = ['image_url', 'review_id']; // Fillable attributes

    /**
     * Define the inverse relationship with the Review model.
     */
    public function review()
    {
        return $this->belongsTo(Review::class);
    }
}
