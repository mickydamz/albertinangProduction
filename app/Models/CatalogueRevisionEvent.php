<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class CatalogueRevisionEvent extends Model {
    protected $guarded = [];
    protected $casts = ['payload'=>'array'];
}
