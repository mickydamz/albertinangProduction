<?php

namespace Tests\Feature;

use Tests\TestCase;

/**
 * The deploy gate: `migrations:check` and the /health/migrations probe must report
 * a healthy schema when the test database is fully migrated. This keeps the guard
 * that protects the payment path from schema drift honest.
 */
class MigrationHealthTest extends TestCase
{
    /** @test */
    public function the_check_command_passes_on_a_fully_migrated_database()
    {
        $this->artisan('migrations:check')
            ->expectsOutputToContain('up to date')
            ->assertExitCode(0);
    }

    /** @test */
    public function the_health_endpoint_reports_ok_when_the_schema_is_current()
    {
        $this->get('/health/migrations')
            ->assertOk()
            ->assertJson(['status' => 'ok']);
    }
}
