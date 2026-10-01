<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'slug')) {
                $table->string('slug')->nullable()->unique();
            }
            if (!Schema::hasColumn('products', 'category_id')) {
                $table->unsignedBigInteger('category_id')->nullable();
            }
            if (!Schema::hasColumn('products', 'subcategory_id')) {
                $table->unsignedBigInteger('subcategory_id')->nullable();
            }
            if (!Schema::hasColumn('products', 'brand_id')) {
                $table->unsignedBigInteger('brand_id')->nullable();
            }
            if (!Schema::hasColumn('products', 'brand')) {
                $table->string('brand')->nullable();
            }
            if (!Schema::hasColumn('products', 'manager_id')) {
                $table->unsignedBigInteger('manager_id')->nullable();
            }
            if (!Schema::hasColumn('products', 'rating')) {
                $table->decimal('rating', 3, 2)->nullable();
            }
            if (!Schema::hasColumn('products', 'moq')) {
                $table->unsignedInteger('moq')->default(1);
            }
            if (!Schema::hasColumn('products', 'location')) {
                $table->string('location')->nullable();
            }
            if (!Schema::hasColumn('products', 'sku')) {
                $table->string('sku')->nullable();
            }
            if (!Schema::hasColumn('products', 'custom_attributes')) {
                $table->json('custom_attributes')->nullable();
            }
            if (!Schema::hasColumn('products', 'custom_attribute_groups')) {
                $table->json('custom_attribute_groups')->nullable();
            }
            if (!Schema::hasColumn('products', 'installation_options')) {
                $table->json('installation_options')->nullable();
            }
            if (!Schema::hasColumn('products', 'installation_option')) {
                $table->string('installation_option')->nullable();
            }
            if (!Schema::hasColumn('products', 'installation_extra_ngn')) {
                $table->integer('installation_extra_ngn')->default(0);
            }
            if (!Schema::hasColumn('products', 'description_blocks')) {
                $table->json('description_blocks')->nullable();
            }
            if (!Schema::hasColumn('products', 'markup_percent')) {
                $table->decimal('markup_percent', 5, 2)->nullable();
            }
            if (!Schema::hasColumn('products', 'discount_percent')) {
                $table->decimal('discount_percent', 5, 2)->nullable();
            }
            if (!Schema::hasColumn('products', 'discount_expires_at')) {
                $table->timestamp('discount_expires_at')->nullable();
            }
            if (!Schema::hasColumn('products', 'is_active')) {
                $table->boolean('is_active')->default(true);
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $cols = ['slug','category_id','subcategory_id','brand_id','brand','manager_id',
                     'rating','moq','location','sku','custom_attributes','custom_attribute_groups',
                     'installation_options','installation_option','installation_extra_ngn',
                     'description_blocks','markup_percent','discount_percent',
                     'discount_expires_at','is_active'];
            $table->dropColumn(array_filter($cols, fn ($c) => Schema::hasColumn('products', $c)));
        });
    }
};
