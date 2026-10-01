<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str; // Import the Str facade for string manipulation

class Tag extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'options', // Add 'options' to the fillable array
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'options' => 'array', // Cast the 'options' attribute to an array
    ];

   

    /**
     * The products that belong to the tag.
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_tag');
    }
}