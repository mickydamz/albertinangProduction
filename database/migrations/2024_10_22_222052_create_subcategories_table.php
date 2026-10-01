<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('subcategories')) {
            return;
        }

        Schema::create('subcategories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->string('name');
            $table->string('slug')->nullable()->unique();
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->decimal('markup_percent', 5, 2)->nullable();
            $table->decimal('discount_percent', 5, 2)->nullable();
            $table->timestamp('discount_expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('requires_truck')->default(false);
            $table->float('estimated_weight_kg')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subcategories');
    }
};
