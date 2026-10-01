<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'fulfillment_method')) {
                $table->string('fulfillment_method')->nullable()->after('pickup_point_address');
            }
            if (!Schema::hasColumn('orders', 'delivery_state_id')) {
                $table->unsignedBigInteger('delivery_state_id')->nullable()->after('fulfillment_method');
            }
            if (!Schema::hasColumn('orders', 'delivery_state_name')) {
                $table->string('delivery_state_name')->nullable()->after('delivery_state_id');
            }
            if (!Schema::hasColumn('orders', 'delivery_location_id')) {
                $table->unsignedBigInteger('delivery_location_id')->nullable()->after('delivery_state_name');
            }
            if (!Schema::hasColumn('orders', 'delivery_location_name')) {
                $table->string('delivery_location_name')->nullable()->after('delivery_location_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'fulfillment_method',
                'delivery_state_id',
                'delivery_state_name',
                'delivery_location_id',
                'delivery_location_name',
            ]);
        });
    }
};
