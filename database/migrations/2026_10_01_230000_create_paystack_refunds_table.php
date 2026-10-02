<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('paystack_refunds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('request_type');
            $table->unsignedBigInteger('request_id');
            $table->string('transaction_reference');
            $table->string('refund_id')->nullable()->unique();
            $table->unsignedBigInteger('amount'); // kobo; full refunds only
            $table->string('currency',3)->default('NGN');
            $table->string('status')->default('requesting')->index();
            $table->timestamp('processed_at')->nullable();
            $table->text('last_error')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('paystack_refunds'); }
};
