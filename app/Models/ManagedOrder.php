<?php
namespace App\Models;
class ManagedOrder extends Order {
 protected $table = 'orders';
 public function items(): \Illuminate\Database\Eloquent\Relations\HasMany { return $this->hasMany(OrderItem::class, 'order_id'); }
 public function return(): \Illuminate\Database\Eloquent\Relations\HasOne { return $this->hasOne(OrderReturn::class, 'order_id'); }
 public function cancellation(): \Illuminate\Database\Eloquent\Relations\HasOne { return $this->hasOne(OrderCancellation::class, 'order_id'); }
 public function getMorphClass() { return Order::class; }

 public function manager() { return $this->belongsTo(User::class, 'assigned_manager_id'); }
 public function activities() { return $this->hasMany(ManagerOrderActivity::class, 'order_id')->latest('id'); }
 public function approvals() { return $this->hasMany(ManagerApprovalRequest::class, 'order_id')->latest('id'); }
 public function paymentConfirmed(): bool {
  if ($this->manager_created) return $this->payment_verified_at !== null;
  return in_array($this->status, ['paid','processing','ready_for_pickup','shipped','delivered','completed'], true);
 }
 public function nextManagerStatuses(): array {
  if (!$this->paymentConfirmed()) return [];
  return match($this->status) {
   'paid' => ['processing'],
   'processing' => $this->fulfillment_method === 'pickup' ? ['ready_for_pickup'] : ['shipped'],
   'ready_for_pickup' => ['completed'], 'shipped' => ['delivered'], 'delivered' => ['completed'], default => []
  };
 }
}
