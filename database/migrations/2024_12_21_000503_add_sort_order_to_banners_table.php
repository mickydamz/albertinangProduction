<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('banners') || Schema::hasColumn('banners', 'sort_order')) {
            return;
        }

        Schema::table('banners', function (Blueprint $table) {
            $table->unsignedInteger('sort_order')->default(0);
        });
    }

    public function down(): void
    {
        if (Schema::hasTable('banners') && Schema::hasColumn('banners', 'sort_order')) {
            Schema::table('banners', function (Blueprint $table) {
                $table->dropColumn('sort_order');
            });
        }
    }
};
