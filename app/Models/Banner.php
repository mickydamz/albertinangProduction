<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',  // Banner type (banner1, banner2, popup, etc.)
        'image', // Image path
        'title', // Banner title
         'link',
         'sort_order',
        'status' // Banner status (active or inactive)
    ];
}
