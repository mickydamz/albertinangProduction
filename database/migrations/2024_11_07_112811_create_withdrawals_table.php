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
        Schema::create('withdrawals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // The user who made the withdrawal
            $table->foreignId('payment_method_id')->constrained()->onDelete('cascade'); // Bank or Crypto method
            $table->decimal('amount', 10, 2); // Withdrawal amount
            $table->text('withdrawal_details'); // Bank details or Crypto address
            $table->enum('status', ['0', '1', '2'])->default('0'); // 0 = Pending, 1 = Completed, 2 = Failed
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
        Schema::dropIfExists('withdrawals');
    }
};
