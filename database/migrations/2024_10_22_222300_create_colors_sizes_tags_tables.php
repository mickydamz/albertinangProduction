<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('colors')) {
            Schema::create('colors', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('sizes')) {
            Schema::create('sizes', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('tags')) {
            Schema::create('tags', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->nullable()->unique();
                $table->json('options')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('product_color')) {
            Schema::create('product_color', function (Blueprint $table) {
                $table->unsignedBigInteger('product_id');
                $table->unsignedBigInteger('color_id');
                $table->primary(['product_id', 'color_id']);
            });
        }

        if (!Schema::hasTable('product_size')) {
            Schema::create('product_size', function (Blueprint $table) {
                $table->unsignedBigInteger('product_id');
                $table->unsignedBigInteger('size_id');
                $table->primary(['product_id', 'size_id']);
            });
        }

        if (!Schema::hasTable('product_tag')) {
            Schema::create('product_tag', function (Blueprint $table) {
                $table->unsignedBigInteger('product_id');
                $table->unsignedBigInteger('tag_id');
                $table->primary(['product_id', 'tag_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('product_tag');
        Schema::dropIfExists('product_size');
        Schema::dropIfExists('product_color');
        Schema::dropIfExists('tags');
        Schema::dropIfExists('sizes');
        Schema::dropIfExists('colors');
    }
};
