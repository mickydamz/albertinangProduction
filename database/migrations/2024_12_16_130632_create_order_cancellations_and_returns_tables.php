<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('order_cancellations')) {
            Schema::create('order_cancellations', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('order_id');
                $table->unsignedBigInteger('user_id')->nullable();
                $table->text('reason')->nullable();
                $table->string('status', 50)->default('pending');
                $table->text('admin_notes')->nullable();
                $table->string('refund_id')->nullable();
                $table->string('refund_status', 50)->nullable();
                $table->timestamp('refunded_at')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('order_returns')) {
            Schema::create('order_returns', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('order_id');
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('status', 50)->default('pending');
                $table->text('reason')->nullable();
                $table->string('evidence_path')->nullable();
                $table->text('admin_notes')->nullable();
                $table->timestamp('reviewed_at')->nullable();
                $table->string('refund_id')->nullable();
                $table->string('refund_status', 50)->nullable();
                $table->timestamp('refunded_at')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('order_returns');
        Schema::dropIfExists('order_cancellations');
    }
};
