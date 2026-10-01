<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('coupons') || Schema::hasColumn('coupons', 'multi_use')) {
            return;
        }

        Schema::table('coupons', function (Blueprint $table) {
            // false = single use per customer, true = a customer may use it multiple times
            $table->boolean('multi_use')->default(false)->after('max_uses');
        });
    }

    public function down(): void
    {
        if (Schema::hasColumn('coupons', 'multi_use')) {
            Schema::table('coupons', function (Blueprint $table) {
                $table->dropColumn('multi_use');
            });
        }
    }
};
