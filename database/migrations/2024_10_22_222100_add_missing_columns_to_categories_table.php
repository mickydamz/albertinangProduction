<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            if (!Schema::hasColumn('categories', 'parent_id')) {
                $table->unsignedBigInteger('parent_id')->nullable();
            }
            if (!Schema::hasColumn('categories', 'slug')) {
                $table->string('slug')->nullable()->unique();
            }
            if (!Schema::hasColumn('categories', 'description')) {
                $table->text('description')->nullable();
            }
            if (!Schema::hasColumn('categories', 'image')) {
                $table->string('image')->nullable();
            }
            if (!Schema::hasColumn('categories', 'markup_percent')) {
                $table->decimal('markup_percent', 5, 2)->nullable();
            }
            if (!Schema::hasColumn('categories', 'discount_percent')) {
                $table->decimal('discount_percent', 5, 2)->nullable();
            }
            if (!Schema::hasColumn('categories', 'discount_expires_at')) {
                $table->timestamp('discount_expires_at')->nullable();
            }
            if (!Schema::hasColumn('categories', 'is_active')) {
                $table->boolean('is_active')->default(true);
            }
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn(array_filter([
                Schema::hasColumn('categories', 'parent_id')         ? 'parent_id'         : null,
                Schema::hasColumn('categories', 'slug')              ? 'slug'              : null,
                Schema::hasColumn('categories', 'description')       ? 'description'       : null,
                Schema::hasColumn('categories', 'image')             ? 'image'             : null,
                Schema::hasColumn('categories', 'markup_percent')    ? 'markup_percent'    : null,
                Schema::hasColumn('categories', 'discount_percent')  ? 'discount_percent'  : null,
                Schema::hasColumn('categories', 'discount_expires_at') ? 'discount_expires_at' : null,
                Schema::hasColumn('categories', 'is_active')         ? 'is_active'         : null,
            ]));
        });
    }
};
