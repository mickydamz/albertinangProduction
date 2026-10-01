<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('banners', function (Blueprint $table) {
            $table->id();
            $table->string('type');  // Type of banner (banner1, banner2, popup, etc.)
            $table->string('image'); // Path to the banner image
            $table->string('title')->nullable();  // Optional title for the banner
            $table->string('link')->nullable();  // Optional title for the banner
            $table->boolean('status')->default(true); // Status to enable/disable the banner
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('banners');
    }
};
