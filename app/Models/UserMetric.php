<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
class UserMetric extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'total_clicks',
        'total_bounties',
        'total_items_shipped',
        'total_earnings',
        'total_orders',
        'clicks',
        'conversions',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }


}
