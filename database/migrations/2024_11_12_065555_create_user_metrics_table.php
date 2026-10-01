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
        Schema::create('user_metrics', function (Blueprint $table) {
            $table->id(); // Auto-increment primary key
            $table->unsignedBigInteger('user_id'); // Foreign key referencing the users table
            $table->integer('total_clicks')->default(0);
            $table->integer('total_bounties')->default(0);
            $table->integer('total_items_shipped')->default(0);
            $table->decimal('total_earnings', 10, 2)->default(0.00);
            $table->integer('total_orders')->default(0);
            $table->integer('clicks')->default(0);
            $table->integer('conversions')->default(0);
            $table->timestamps();

            // Define foreign key relationship to users table
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_metrics');
    }
};
