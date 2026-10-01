<?php

namespace App;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Product;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'city',
        'postal_code',
        'shipping_address',
        'account_balance',
        'avatar',
        'fileName',
        'fileTitle',
        'find_us',
        'country',
        'country_id',
        'phone_no'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    public function sentMessages()
{
    return $this->hasMany(Message::class, 'sender_id');
}

public function receivedMessages()
{
    return $this->hasMany(Message::class, 'recipient_id');
}

public function products()
    {
        return $this->hasMany(Product::class, 'supplier_id');
    }

    public function country()
{
    return $this->belongsTo(Country::class);
}

public function countryRelation()
    {
        return $this->belongsTo(Country::class);
    }

    public function hasRole($role)
    {
        return $this->role === $role;
    }


    public function incrementBalance($amount)
{
    $this->account_balance += $amount;
    $this->save();
}

public function decrementBalance($amount)
{
    if ($this->account_balance >= $amount) {
        $this->account_balance -= $amount;
        $this->save();
    }
}


public function deposits()
{
    return $this->hasMany(Deposit::class);
}

}
