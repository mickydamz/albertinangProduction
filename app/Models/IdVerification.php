<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class IdVerification extends Model
{
    use HasFactory;
    // protected $fillable = ['user_id','type','front', 'back', 'status','status'];

    protected $guarded = [];


    public function user(){
        return $this->belongsTo(User::class);
    }




    public function isVerified()
    {
        return $this->status === true;
    }
}
