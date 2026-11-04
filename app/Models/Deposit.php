<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deposit extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'payment_method_id',
        'country_id',
        'crypto_currency',
        'amount',
        'transaction_picture',
        'status'
    ];

    // Relationship with User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relationship with PaymentMethod
    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    // Relationship with Country
    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}
