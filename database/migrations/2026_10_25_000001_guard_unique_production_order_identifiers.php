<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        $missing = [];
        foreach (['reference', 'order_number'] as $column) {
            if ($this->hasSingleColumnUniqueIndex($column)) { continue; }
            if (DB::table('orders')->select($column)->whereNotNull($column)
                ->groupBy($column)->havingRaw('COUNT(*) > 1')->exists()) {
                throw new RuntimeException('Duplicate order '.$column.' values require review; no orders were deleted.');
            }
            $missing[] = $column;
        }
        // Each unique-index creation is atomic. A concurrent duplicate causes
        // the database to reject that index, never to remove an existing order.
        foreach ($missing as $column) {
            Schema::table('orders', function (Blueprint $table) use ($column) {
                $table->unique($column, 'orders_'.$column.'_unique');
            });
        }
    }

    private function hasSingleColumnUniqueIndex(string $column): bool
    {
        $indexes = [];
        if (DB::getDriverName() === 'mysql') {
            foreach (DB::select('SHOW INDEX FROM orders') as $row) {
                if ((int) $row->Non_unique !== 0 || $row->Sub_part !== null) { continue; }
                $indexes[$row->Key_name][(int) $row->Seq_in_index] = $row->Column_name;
            }
        } elseif (DB::getDriverName() === 'sqlite') {
            foreach (DB::select("PRAGMA index_list('orders')") as $row) {
                if (!(int) $row->unique || (int) ($row->partial ?? 0)) { continue; }
                $quoted = str_replace("'", "''", $row->name);
                foreach (DB::select("PRAGMA index_info('{$quoted}')") as $field) {
                    $indexes[$row->name][(int) $field->seqno] = $field->name;
                }
            }
        } else {
            throw new RuntimeException('Unsupported database driver; inspect order indexes manually.');
        }
        foreach ($indexes as $fields) {
            ksort($fields);
            if (array_values($fields) === [$column]) { return true; }
        }
        return false;
    }

    public function down(): void
    {
        throw new RuntimeException('Retain payment uniqueness constraints during code rollback; schema rollback requires a separate review.');
    }
};
