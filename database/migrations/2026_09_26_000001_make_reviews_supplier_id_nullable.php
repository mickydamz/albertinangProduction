<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * A product review is written by a customer about a product — it is not tied to
 * a supplier. The original reviews table made supplier_id NOT NULL, which meant
 * every review insert (storefront ProductController flow AND the admin panel)
 * would fail because neither path supplies a supplier. Make it nullable so
 * reviews can actually be saved.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->unsignedBigInteger('supplier_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->unsignedBigInteger('supplier_id')->nullable(false)->change();
        });
    }
};
