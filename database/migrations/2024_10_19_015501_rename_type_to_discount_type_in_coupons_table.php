<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('coupons')) {
            return;
        }

        Schema::table('coupons', function (Blueprint $table) {
            if (Schema::hasColumn('coupons', 'type') && !Schema::hasColumn('coupons', 'discount_type')) {
                $table->renameColumn('type', 'discount_type');
            }
            // Dump has 0.00 for min_order_amount — ensure it allows zero values (not just NULL).
            if (Schema::hasColumn('coupons', 'min_order_amount')) {
                $table->decimal('min_order_amount', 10, 2)->default(0)->nullable()->change();
            }
        });
    }

    public function down(): void
    {
        Schema::table('coupons', function (Blueprint $table) {
            if (Schema::hasColumn('coupons', 'discount_type') && !Schema::hasColumn('coupons', 'type')) {
                $table->renameColumn('discount_type', 'type');
            }
        });
    }
};
