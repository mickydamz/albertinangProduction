<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_of_card',
        'amount',
        'response_code',
        'transaction_id',
        'auth_id',
        'message_code',
        'qty'
    ];
}