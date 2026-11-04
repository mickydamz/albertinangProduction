<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void {
  Schema::table('orders', function(Blueprint $t) {
   $t->unsignedBigInteger('assigned_manager_id')->nullable()->index();
   $t->unsignedBigInteger('created_by_staff_id')->nullable();
   $t->boolean('manager_created')->default(false);
   $t->timestamp('payment_verified_at')->nullable();
   $t->unsignedBigInteger('payment_verified_by')->nullable();
   $t->string('courier_name')->nullable(); $t->string('tracking_number')->nullable();
  });
  Schema::create('manager_order_activities', function(Blueprint $t) {
   $t->id(); $t->unsignedBigInteger('order_id')->index(); $t->unsignedBigInteger('user_id')->nullable();
   $t->string('user_name'); $t->string('action'); $t->text('details'); $t->timestamps();
  });
  Schema::create('manager_approval_requests', function(Blueprint $t) {
   $t->id(); $t->unsignedBigInteger('order_id')->nullable()->index(); $t->unsignedBigInteger('product_id')->nullable();
   $t->unsignedBigInteger('requested_by'); $t->string('type'); $t->string('status')->default('pending')->index();
   $t->json('payload'); $t->text('reason'); $t->unsignedBigInteger('reviewed_by')->nullable();
   $t->text('review_notes')->nullable(); $t->timestamp('reviewed_at')->nullable(); $t->timestamps();
  });
 }
 public function down(): void {
  Schema::dropIfExists('manager_approval_requests'); Schema::dropIfExists('manager_order_activities');
  Schema::table('orders', fn(Blueprint $t) => $t->dropColumn(['assigned_manager_id','created_by_staff_id','manager_created','payment_verified_at','payment_verified_by','courier_name','tracking_number']));
 }
};
