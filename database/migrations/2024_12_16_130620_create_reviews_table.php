<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReviewsTable extends Migration
{
    public function up()
    {
        Schema::create('reviews', function (Blueprint $table) {
        
            $table->id(); // BIGINT UNSIGNED AUTO_INCREMENT
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // References users(id)
            $table->foreignId('supplier_id')->constrained('users')->onDelete('cascade'); // References users(id)
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade'); // References products(id)
            $table->string('image')->nullable(); // Optional image field, as per model
            $table->text('content'); // Review content
            $table->unsignedInteger('rating'); // Rating (e.g., 1-5)
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('reviews');
    }
}