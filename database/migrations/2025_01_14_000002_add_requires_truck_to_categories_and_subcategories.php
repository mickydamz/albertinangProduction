<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('categories') && !Schema::hasColumn('categories', 'requires_truck')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->boolean('requires_truck')->default(false)
                    ->comment('Products in this category require a delivery truck (e.g. refrigerators, cooling units)');
            });
        }

        if (Schema::hasTable('subcategories') && !Schema::hasColumn('subcategories', 'requires_truck')) {
            Schema::table('subcategories', function (Blueprint $table) {
                $table->boolean('requires_truck')->default(false)
                    ->comment('Products in this subcategory require a delivery truck');
            });
        }
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            if (Schema::hasColumn('categories', 'requires_truck')) {
                $table->dropColumn('requires_truck');
            }
        });

        Schema::table('subcategories', function (Blueprint $table) {
            if (Schema::hasColumn('subcategories', 'requires_truck')) {
                $table->dropColumn('requires_truck');
            }
        });
    }
};
