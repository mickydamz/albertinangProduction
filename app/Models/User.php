<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Auth\Notifications\VerifyEmail;
use App\Notifications\ResetPasswordNotification;


class User extends Authenticatable implements MustVerifyEmail
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
        'state',
        'description',
        'password',
        'role',
        'city',
        'postal_code',
        'shipping_address',
        'account_balance',
        'phone_no',
        'avatar',
        'fileName',
        'find_us',
        'country',
        'country_id',
        'state_id',
        'status',
        'two_factor_code', 
        'two_factor_expires_at', 
        'two_factor_enabled',
        'referred_by',
        'affiliate_code',
        'description',
        'total_clicks',
        'total_bounties',
        'total_items_shipped',
        'total_earnings',
        'total_orders',
        'clicks',
        'conversions',
        'is_hidden',
        'date_of_birth',     // ← add this new one

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

    public function managedProducts()
{
    return $this->hasMany(Product::class, 'manager_id');
}

    public function isTwoFactorCodeExpired()
    {
        return $this->two_factor_expires_at->lt(Carbon::now());
    }

    /**
     * Reset the 2FA code and expiration.
     *
     * @return void
     */
    public function resetTwoFactorCode()
    {
        $this->two_factor_code = null;
        $this->two_factor_expires_at = null;
        $this->save();
    }

    public function reset2FA()
    {
        $this->two_factor_code = null;
        $this->two_factor_expires_at = null;
        $this->save();
    }

  // Define the relationship to track users referred by this user
  public function referrals()
  {
      return $this->hasMany(User::class, 'referred_by');
  }

  // Define the relationship to track the user who referred this user
  public function referredBy()
  {
      return $this->belongsTo(User::class, 'referred_by');
  }


  public function orders()
{
    return $this->hasMany(\App\Models\Order::class, 'user_id');
}

// public function addresses()
// {
//     return $this->hasMany(\App\Models\Address::class, 'user_id');
// }

  // Supplier's received reviews
  public function receivedReviews()
  {
      return $this->hasMany(Review::class, 'supplier_id');
  }

  // User's written reviews
  public function writtenReviews()
  {
      return $this->hasMany(Review::class, 'user_id');
  }

    public function sentMessages()
{
    return $this->hasMany(Message::class, 'sender_id');
}

public function reviews()
{
    return $this->hasMany(Review::class, 'supplier_id');
}


public function receivedMessages()
{
    return $this->hasMany(Message::class, 'recipient_id'); // Ensure this matches your database
}


public function products()
    {
        return $this->hasMany(Product::class, 'supplier_id');
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function state()
    {
        return $this->belongsTo(City::class, 'state_id');
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



public function sendEmailVerificationNotification()
{
    $this->notify(new \App\Notifications\VerifyEmailNotification);
}



public function sendPasswordResetNotification($token)
{
    $this->notify(new ResetPasswordNotification($token));
}

}
