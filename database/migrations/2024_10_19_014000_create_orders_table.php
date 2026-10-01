<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('orders')) {
            return;
        }

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('status')->default('pending');
            $table->decimal('total', 12, 2)->default(0);
            $table->decimal('total_usd', 12, 2)->default(0);
            $table->decimal('shipping_cost', 10, 2)->default(0);
            $table->string('payment_method')->nullable();
            $table->string('payment_id')->nullable();
            $table->string('reference')->nullable();
            $table->string('order_number')->nullable()->unique();
            $table->string('fulfillment_method')->nullable();
            $table->string('pickup_location')->nullable();
            $table->unsignedBigInteger('pickup_location_id')->nullable();
            $table->unsignedBigInteger('pickup_point_id')->nullable();
            $table->string('pickup_point_name')->nullable();
            $table->string('pickup_point_address')->nullable();
            $table->unsignedBigInteger('delivery_state_id')->nullable();
            $table->string('delivery_state_name')->nullable();
            $table->unsignedBigInteger('delivery_location_id')->nullable();
            $table->string('delivery_location_name')->nullable();
            $table->text('shipping_address')->nullable();
            $table->string('customer_email')->nullable();
            $table->unsignedBigInteger('coupon_id')->nullable();
            $table->string('coupon_code_used')->nullable();
            $table->decimal('coupon_discount_ngn', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
