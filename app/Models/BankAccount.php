<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Product;
use App\Models\User;
use App\Models\Country;

class BankAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'account_number',
        'swift_code',
        'address',
        'country',
        'is_active',
        'country_id'
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}
