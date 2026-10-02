<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('pending_checkouts', function (Blueprint $table) {
            $table->string('gateway')->default('paystack');
            $table->string('payment_intent_id')->nullable()->unique();
            $table->unsignedBigInteger('gateway_amount')->nullable();
            $table->timestamp('expires_at')->nullable()->index();
            $table->timestamp('payment_confirmed_at')->nullable();
            $table->text('recovery_error')->nullable();
        });
        Schema::table('orders', function (Blueprint $table) {
            $table->timestamp('delivered_at')->nullable();
            $table->string('tracking_reference')->nullable();
            $table->timestamp('refund_requested_at')->nullable();
            $table->unique(['payment_method', 'payment_id']);
        });
        Schema::create('refund_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('request_key')->unique();
            $table->string('gateway');
            $table->decimal('amount', 12, 2);
            $table->unsignedBigInteger('gateway_amount');
            $table->string('gateway_id')->nullable();
            $table->string('status')->default('requested');
            $table->text('failure_reason')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
        Schema::create('order_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('mailable');
            $table->string('recipient');
            $table->string('event_key')->unique();
            $table->timestamp('sent_at')->nullable();
            $table->unsignedInteger('attempts')->default(0);
            $table->text('last_error')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_notifications');
        Schema::dropIfExists('refund_attempts');
        Schema::table('orders', function (Blueprint $table) {
            $table->dropUnique(['payment_method', 'payment_id']);
            $table->dropColumn(['delivered_at', 'tracking_reference', 'refund_requested_at']);
        });
        Schema::table('pending_checkouts', fn (Blueprint $table) => $table->dropColumn([
            'gateway', 'payment_intent_id', 'gateway_amount', 'expires_at', 'payment_confirmed_at', 'recovery_error',
        ]));
    }
};
