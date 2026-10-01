<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;

    // Fillable attributes for mass assignment
    protected $fillable = ['name', 'country_id'];

    /**
     * Define the relationship with the Country model.
     * A City belongs to one Country.
     */
    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}
