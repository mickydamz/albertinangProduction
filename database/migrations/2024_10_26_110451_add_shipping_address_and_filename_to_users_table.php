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
        Schema::table('users', function (Blueprint $table) {
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('country')->nullable(); // Add country here
            $table->string('phone_no')->nullable(); // Add phone number
            $table->string('fileName')->nullable(); // Add filename
            $table->string('find_us')->nullable(); // Add find us
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['shipping_address', 'city', 'state', 'postal_code', 'country', 'phone_no', 'fileName', 'find_us']);
        });
    }
};
