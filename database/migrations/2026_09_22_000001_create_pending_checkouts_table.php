<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pending_checkouts', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('customer_email')->nullable();
            $table->unsignedBigInteger('total_ngn');
            $table->json('items');
            $table->json('fulfillment')->nullable();
            $table->json('coupon')->nullable();
            $table->timestamp('fulfilled_at')->nullable();
            $table->timestamps();

            $table->index('reference');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pending_checkouts');
    }
};
