<?php
// app/Models/PickupPoint.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PickupPoint extends Model
{
    use HasFactory;

    protected $fillable = ['location_id', 'name', 'address', 'hours', 'is_active','store_location_id','state_id'];

    protected static function booted() {
        static::creating(function($point){if(!$point->state_id && $point->location_id)$point->state_id=Location::find($point->location_id)?->state_id;});
    }
    public function products() { return $this->belongsToMany(Product::class, 'product_pickup_point'); }
    public function state() { return $this->belongsTo(State::class); }
    public function location()
    {
        return $this->belongsTo(Location::class);
    }
}
