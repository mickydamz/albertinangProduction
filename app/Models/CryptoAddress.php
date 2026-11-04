<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CryptoAddress extends Model
{
    use HasFactory;


    protected $fillable = [
        'currency',
        'amount',
        'address',
        // other fields...
    ];
}
