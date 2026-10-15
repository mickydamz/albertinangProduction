<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
        $table->timestamp('marketing_subscribed_at')->nullable();
        });
        Schema::create('marketing_campaigns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('subject', 150);
            $table->text('message');
            $table->string('offer_url', 2048)->nullable();
            $table->string('status')->default('draft');
            $table->timestamps();
        });
        Schema::create('marketing_deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('marketing_campaign_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('email');
            $table->string('status')->default('pending');
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
            $table->unique(['marketing_campaign_id', 'user_id']);
            $table->index('status');
        });
    }

    public function down()
    {
        Schema::dropIfExists('marketing_deliveries');
        Schema::dropIfExists('marketing_campaigns');
        Schema::table('users', function (Blueprint $table) {
        $table->dropColumn('marketing_subscribed_at');
        });
    }
};
