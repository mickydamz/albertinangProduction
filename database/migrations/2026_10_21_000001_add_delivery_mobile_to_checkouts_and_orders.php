<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        foreach (['pending_checkouts', 'orders'] as $name) {
            Schema::table($name, function (Blueprint $table) { $table->string('delivery_mobile', 16)->nullable(); });
        }
    }
    public function down(): void {
        foreach (['pending_checkouts', 'orders'] as $name) {
            Schema::table($name, function (Blueprint $table) { $table->dropColumn('delivery_mobile'); });
        }
    }
};
