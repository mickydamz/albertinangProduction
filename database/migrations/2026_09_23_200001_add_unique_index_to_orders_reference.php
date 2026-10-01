<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Remove any duplicate references before adding the constraint —
        // keep the earliest order per reference (lowest id).
        $duplicates = DB::table('orders')
            ->select('reference')
            ->whereNotNull('reference')
            ->groupBy('reference')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('reference');

        foreach ($duplicates as $reference) {
            $keepId = DB::table('orders')
                ->where('reference', $reference)
                ->orderBy('id')
                ->value('id');

            DB::table('orders')
                ->where('reference', $reference)
                ->where('id', '!=', $keepId)
                ->delete();
        }

        try {
            Schema::table('orders', function (Blueprint $table) {
                // Unique constraint prevents race-condition double-orders when the
                // webhook and the browser confirm-order call arrive simultaneously.
                $table->unique('reference');
            });
        } catch (\Exception $e) {
            // Index may already exist — safe to ignore.
        }
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropUnique(['reference']);
        });
    }
};
