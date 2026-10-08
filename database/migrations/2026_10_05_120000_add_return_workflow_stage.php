<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::table('order_returns', function (Blueprint $table) {
            $table->string('workflow_stage')->nullable();
            $table->text('return_instructions')->nullable();
        });
        Schema::create('order_request_events', function (Blueprint $table) {
            $table->id(); $table->foreignId('order_id'); $table->string('request_type');
            $table->unsignedBigInteger('request_id'); $table->unsignedBigInteger('actor_id')->nullable();
            $table->string('action'); $table->text('notes')->nullable(); $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('order_request_events');
        Schema::table('order_returns', fn (Blueprint $table) => $table->dropColumn(['workflow_stage','return_instructions']));
    }
};
