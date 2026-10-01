<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('categories') && !Schema::hasColumn('categories', 'estimated_weight_kg')) {
            Schema::table('categories', fn (Blueprint $t) => $t->float('estimated_weight_kg')->nullable());
        }

        if (Schema::hasTable('subcategories') && !Schema::hasColumn('subcategories', 'estimated_weight_kg')) {
            Schema::table('subcategories', fn (Blueprint $t) => $t->float('estimated_weight_kg')->nullable());
        }

        if (Schema::hasTable('products') && !Schema::hasColumn('products', 'weight_kg')) {
            Schema::table('products', fn (Blueprint $t) => $t->float('weight_kg')->nullable());
        }

        // Seed default thresholds — upsert so re-running is safe
        DB::table('settings')->upsert(
            [
                ['key' => 'truck_weight_threshold_kg',       'value' => '30',      'created_at' => now(), 'updated_at' => now()],
                ['key' => 'truck_order_value_threshold_ngn', 'value' => '1000000', 'created_at' => now(), 'updated_at' => now()],
            ],
            ['key'],
            ['value', 'updated_at']
        );
    }

    public function down(): void
    {
        Schema::table('categories',    fn (Blueprint $t) => $t->dropColumn('estimated_weight_kg'));
        Schema::table('subcategories', fn (Blueprint $t) => $t->dropColumn('estimated_weight_kg'));
        Schema::table('products',      fn (Blueprint $t) => $t->dropColumn('weight_kg'));

        DB::table('settings')
            ->whereIn('key', ['truck_weight_threshold_kg', 'truck_order_value_threshold_ngn'])
            ->delete();
    }
};
