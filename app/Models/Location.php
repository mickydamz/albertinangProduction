<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

    protected $fillable = [
        'state_id',
        'name',
        'is_active',
        'shipping_cost',
        'truck_shipping_cost',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_location', 'location_id', 'product_id');
    }

    public function pickupPoints()
    {
        return $this->hasMany(PickupPoint::class);
    }

    public function state() 
    { 
        return $this->belongsTo(State::class); 
    }
}