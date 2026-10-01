<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('product_location')) {
            return;
        }

        Schema::create('product_location', function (Blueprint $table) {
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('location_id');
            $table->primary(['product_id', 'location_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_location');
    }
};
