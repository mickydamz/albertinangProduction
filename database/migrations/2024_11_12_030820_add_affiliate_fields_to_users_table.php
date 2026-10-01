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
            $table->unsignedBigInteger('total_clicks')->default(0);
            $table->unsignedBigInteger('total_bounties')->default(0);
            $table->unsignedBigInteger('total_items_shipped')->default(0);
            $table->decimal('total_earnings', 10, 2)->default(0.00);
            $table->unsignedBigInteger('total_orders')->default(0);
            $table->unsignedBigInteger('clicks')->default(0);
            $table->unsignedBigInteger('conversions')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {Schema::table('users', function (Blueprint $table) {
        $table->dropColumn([
            'total_clicks',
            'total_bounties',
            'total_items_shipped',
            'total_earnings',
            'total_orders',
            'clicks',
            'conversions',
        ]);
    });
    }
};
