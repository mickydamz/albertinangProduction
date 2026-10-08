<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\{Schema, DB};

return new class extends Migration {
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->timestamp('received_at')->nullable();
        });
        // Recover actual receipt transitions from the audit trail. Unknown dates
        // remain null so an admin can verify them rather than inventing a deadline.
        if (Schema::hasTable('audit_logs')) {
            DB::table('audit_logs')->where('auditable_type', 'App\\Models\\Order')
                ->orderBy('id')->chunkById(500, function ($logs) {
                    foreach ($logs as $log) {
                        $values = json_decode($log->new_values ?? '{}', true);
                        if (in_array($values['status'] ?? null, ['delivered', 'completed'], true)) {
                            DB::table('orders')->where('id', $log->auditable_id)
                                ->whereNull('received_at')->update(['received_at'=>$log->created_at]);
                        }
                    }
                });
        }
    }
    public function down(): void
    {
        Schema::table('orders', fn(Blueprint $table) => $table->dropColumn('received_at'));
    }
};
