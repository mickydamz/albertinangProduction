<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Withdrawal extends Model
{
    use HasFactory;

    // Define the table associated with the model (optional)
    protected $table = 'withdrawals';

    // Mass assignable attributes
    protected $fillable = [
        'user_id',
        'payment_method_id',
        'amount',
        'withdrawal_details',
        'status',
    ];

    // Define the relationship with the User model
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Define the relationship with the PaymentMethod model
    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }
}
