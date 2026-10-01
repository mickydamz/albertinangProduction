<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('locations')) {
            return;
        }
        Schema::table('locations', function (Blueprint $table) {
            if (!Schema::hasColumn('locations', 'shipping_cost')) {
                $table->decimal('shipping_cost', 10, 2)->nullable()->after('is_active');
            }
            if (!Schema::hasColumn('locations', 'truck_shipping_cost')) {
                $table->decimal('truck_shipping_cost', 10, 2)->nullable()->after('shipping_cost')
                    ->comment('Delivery fee for refrigerator/cooling products requiring a truck');
            }
        });
    }

    public function down(): void
    {
        Schema::table('locations', function (Blueprint $table) {
            if (Schema::hasColumn('locations', 'truck_shipping_cost')) {
                $table->dropColumn('truck_shipping_cost');
            }
        });
    }
};
