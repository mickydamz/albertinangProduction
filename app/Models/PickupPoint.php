<?php
// app/Models/PickupPoint.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PickupPoint extends Model
{
    use HasFactory;

    protected $fillable = ['location_id', 'name', 'address', 'hours'];

    public function location()
    {
        return $this->belongsTo(Location::class);
    }
}