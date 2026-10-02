<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

/**
 * Deploy gate / health check for schema drift.
 *
 * The payment path (checkout, webhooks, browser-confirm, refunds, notifications,
 * reconciliation) hard-depends on its migrations. A deploy that ships the code
 * without running them 500s the money path — and the webhook mode fails silently
 * (payment taken, no order). This command makes that condition loud: it exits
 * non-zero when any migration is pending, so a deploy step can refuse to go live.
 *
 *   php artisan migrations:check   # exit 0 = up to date, 1 = pending / not set up
 */
class CheckMigrations extends Command
{
    protected $signature = 'migrations:check';
    protected $description = 'Fail (non-zero) if any database migration is pending — use as a deploy gate';

    public function handle(): int
    {
        // Resolve the pre-wired singleton — the bare Migrator class is not
        // container-instantiable (its repository dependency isn't bound by type).
        $migrator = $this->laravel->make('migrator');

        if (! $migrator->repositoryExists()) {
            $this->error('Migration repository not found. Run `php artisan migrate --force`.');

            return self::FAILURE;
        }

        $ran     = $migrator->getRepository()->getRan();
        $files   = $migrator->getMigrationFiles($migrator->paths() ?: [database_path('migrations')]);
        $pending = array_diff(array_keys($files), $ran);

        if (! empty($pending)) {
            $this->error(count($pending) . ' pending migration(s) — schema is behind the code:');
            foreach ($pending as $name) {
                $this->line('  - ' . $name);
            }
            $this->newLine();
            $this->error('Refusing readiness. Run `php artisan migrate --force` before serving traffic.');

            return self::FAILURE;
        }

        $this->info('Database schema is up to date (' . count($ran) . ' migrations applied).');

        return self::SUCCESS;
    }
}
