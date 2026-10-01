<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Refund evidence. The order now carries a complete, verifiable financial record
 * of any refund (gateway id, status, amount, when it settled, and why it failed)
 * independent of which return/cancellation triggered it. Returns/cancellations
 * already stored id/status/refunded_at; we add amount + failure reason there too.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('refund_id')->nullable()->after('reference');
            $table->string('refund_status', 50)->nullable()->after('refund_id');
            $table->decimal('refund_amount', 12, 2)->nullable()->after('refund_status');
            $table->timestamp('refunded_at')->nullable()->after('refund_amount');
            $table->text('refund_failure_reason')->nullable()->after('refunded_at');
        });

        foreach (['order_returns', 'order_cancellations'] as $t) {
            Schema::table($t, function (Blueprint $table) {
                $table->decimal('refund_amount', 12, 2)->nullable()->after('refund_status');
                $table->text('refund_failure_reason')->nullable()->after('refunded_at');
            });
        }
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'refund_id', 'refund_status', 'refund_amount', 'refunded_at', 'refund_failure_reason',
            ]);
        });

        foreach (['order_returns', 'order_cancellations'] as $t) {
            Schema::table($t, function (Blueprint $table) {
                $table->dropColumn(['refund_amount', 'refund_failure_reason']);
            });
        }
    }
};
