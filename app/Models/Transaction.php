<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Product;
use App\Models\User;
use App\Models\PaymentMethod;



class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'payment_method_id',
        'total_amount',
        'status',
        'escrow_amount',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function products()
{
    return $this->belongsToMany(Product::class, 'transaction_product')
                ->withPivot('quantity', 'price') // Include additional columns
                ->withTimestamps();
}

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }
}
