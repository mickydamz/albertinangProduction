<?php
namespace App\Support;
class ShoppingCache {
 public static function remember($key,$ttl,$callback){return $callback();}
}
