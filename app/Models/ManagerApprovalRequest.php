<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class ManagerApprovalRequest extends Model {
 use \App\Traits\Auditable;
 protected $guarded = [];
 protected $casts = ['payload'=>'array','reviewed_at'=>'datetime'];
 public function order() { return $this->belongsTo(ManagedOrder::class, 'order_id'); }
 public function product() { return $this->belongsTo(Product::class); }
 public function requester() { return $this->belongsTo(User::class, 'requested_by'); }
}
