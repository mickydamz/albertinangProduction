<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Preserve the same migration on SQLite: drop the supplier FK and
        // make supplier_id nullable using DBAL's table-rebuild support.
        $connection = Schema::getConnection();
        if ($connection->getDriverName() === 'sqlite') {
            $manager = $connection->getDoctrineSchemaManager();
            $before = $manager->introspectTable('products');
            $after = clone $before;
            foreach ($after->getForeignKeys() as $foreignKeyName => $foreignKey) {
                if (in_array('supplier_id', $foreignKey->getLocalColumns(), true)) {
                    $after->removeForeignKey($foreignKeyName);
                }
            }
            $after->getColumn('supplier_id')->setNotnull(false);
            $diff = $manager->createComparator()->compareTables($before, $after);
            foreach ($connection->getDoctrineConnection()->getDatabasePlatform()->getAlterTableSQL($diff) as $sql) {
                $connection->statement($sql);
            }
            return;
        }

        Schema::table('products', function (Blueprint $table) {
            // Drop the strict FK so products can exist without a matching user row.
            if ($this->fkExists('products', 'products_supplier_id_foreign')) {
                $table->dropForeign('products_supplier_id_foreign');
            }

            // Make nullable so products don't require a supplier account.
            $table->unsignedBigInteger('supplier_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->unsignedBigInteger('supplier_id')->nullable(false)->change();
            $table->foreign('supplier_id')->references('id')->on('users');
        });
    }

    private function fkExists(string $table, string $fkName): bool
    {
        $conn   = Schema::getConnection();
        $dbName = $conn->getDatabaseName();
        $count  = $conn->table('information_schema.KEY_COLUMN_USAGE')
            ->where('TABLE_SCHEMA', $dbName)
            ->where('TABLE_NAME', $table)
            ->where('CONSTRAINT_NAME', $fkName)
            ->count();
        return $count > 0;
    }
};
