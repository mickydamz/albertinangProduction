<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'requires_truck')) {
                $table->boolean('requires_truck')->nullable()->default(null)
                    ->comment('null = inherit from category/subcategory; true = always truck; false = never truck');
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'requires_truck')) {
                $table->dropColumn('requires_truck');
            }
        });
    }
};
