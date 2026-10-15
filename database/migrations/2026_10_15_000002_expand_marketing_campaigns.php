<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('marketing_campaigns', function (Blueprint $t) {
            $t->longText('body_html')->nullable();
            $t->string('preview_text', 200)->nullable();
            $t->string('button_text', 60)->default('Shop now');
            $t->string('template')->default('classic');
            $t->json('images')->nullable();
            $t->json('brand')->nullable();
            $t->string('audience')->default('all');
            $t->json('selected_users')->nullable();
            $t->timestamp('scheduled_at')->nullable();
            $t->timestamp('tested_at')->nullable();
        });
        Schema::table('marketing_deliveries', function (Blueprint $t) {
        $t->timestamp('clicked_at')->nullable();
        $t->timestamp('unsubscribed_at')->nullable();
        $t->unsignedInteger('click_count')->default(0);
        });
    }

    public function down()
    {
        Schema::table('marketing_deliveries', fn (Blueprint $t) => $t->dropColumn(['clicked_at', 'click_count', 'unsubscribed_at']));
        Schema::table('marketing_campaigns', fn (Blueprint $t) => $t->dropColumn(['body_html', 'preview_text', 'button_text', 'template', 'images', 'brand', 'audience', 'selected_users', 'scheduled_at', 'tested_at']));
    }
};
