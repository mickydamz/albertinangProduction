<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('pending_checkouts', fn (Blueprint $table) => $table->uuid('request_key')->nullable()->unique());
        Schema::table('refund_attempts', fn (Blueprint $table) => $table->string('gateway_status')->nullable());
        Schema::table('order_returns', fn (Blueprint $table) => $table->unique('order_id'));
        Schema::table('order_cancellations', fn (Blueprint $table) => $table->unique('order_id'));
    }
    public function down(): void
    {
        Schema::table('pending_checkouts', fn (Blueprint $table) => $table->dropColumn('request_key'));
        Schema::table('refund_attempts', fn (Blueprint $table) => $table->dropColumn('gateway_status'));
        Schema::table('order_returns', fn (Blueprint $table) => $table->dropUnique(['order_id']));
        Schema::table('order_cancellations', fn (Blueprint $table) => $table->dropUnique(['order_id']));
    }
};
