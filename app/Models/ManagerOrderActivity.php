<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class ManagerOrderActivity extends Model {
 protected $guarded = [];
 public static function record(ManagedOrder $order, string $action, string $details): void {
  static::create(['order_id'=>$order->id,'user_id'=>auth()->id(),'user_name'=>auth()->user()?->name ?? 'System','action'=>$action,'details'=>$details]);
 }
}
