<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'stock_status')) {
                // Values: 'in_stock', 'out_of_stock', 'pre_order'
                $table->string('stock_status', 20)->default('in_stock');
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'stock_status')) {
                $table->dropColumn('stock_status');
            }
        });
    }
};
