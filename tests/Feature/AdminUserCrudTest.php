<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

/**
 * Admin user management CRUD (AdminUserController store / update / destroy).
 */
class AdminUserCrudTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
        $this->withoutMiddleware(ThrottleRequests::class);
    }

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    /** @test */
    public function an_admin_can_create_a_user()
    {
        $this->actingAs($this->admin())->post('/admin/users', [
            'name'                  => 'Created User',
            'email'                 => 'created@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
            'role'                  => 'user',
            'status'                => 'green',
            'verified'              => 1,
        ])->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseHas('users', ['email' => 'created@example.com', 'role' => 'user']);
    }

    /** @test */
    public function creating_a_user_validates_required_fields_and_unique_email()
    {
        $existing = User::factory()->create(['email' => 'taken@example.com']);

        $this->actingAs($this->admin())->from('/admin/users/create')->post('/admin/users', [
            'email' => 'taken@example.com', // duplicate; other required fields missing
        ])->assertSessionHasErrors(['name', 'email', 'password', 'role', 'status']);
    }

    /** @test */
    public function an_admin_can_update_a_user()
    {
        $user = User::factory()->create(['name' => 'Old Name']);

        $this->actingAs($this->admin())->put("/admin/users/{$user->id}", [
            'name'            => 'New Name',
            'email'           => $user->email, // unchanged — unique rule ignores self
            'role'            => 'user',
            'status'          => 'green',
            'verified'        => 1,
            'account_balance' => 0,
        ])->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseHas('users', ['id' => $user->id, 'name' => 'New Name']);
    }

    /** @test */
    public function an_admin_can_delete_a_user()
    {
        $user = User::factory()->create();

        $this->actingAs($this->admin())->delete("/admin/users/{$user->id}")
            ->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }
}
