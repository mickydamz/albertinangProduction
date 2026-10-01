<?php

namespace Tests\Feature;

use App\Models\Currency;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AdminCurrencyFormTest extends TestCase
{
    use DatabaseTransactions;

    private function admin(): User
    {
        $u = User::factory()->create();
        $u->role = 'admin';
        $u->save();
        return $u;
    }

    /** @test */
    public function create_currency_page_renders()
    {
        $this->actingAs($this->admin())
            ->get(route('admin.currencies.create'))
            ->assertOk()
            ->assertSee('Add New Currency');
    }

    /** @test */
    public function edit_currency_page_renders()
    {
        $currency = Currency::create([
            'code' => 'USD', 'symbol' => '$', 'name' => 'US Dollar',
            'rate_to_ngn' => 1500, 'is_active' => true, 'sort_order' => 1,
        ]);

        $this->actingAs($this->admin())
            ->get(route('admin.currencies.edit', $currency))
            ->assertOk()
            ->assertSee('Edit Currency')
            ->assertSee('US Dollar');
    }

    /** @test */
    public function admin_can_update_a_currency()
    {
        $currency = Currency::create([
            'code' => 'GBP', 'symbol' => '£', 'name' => 'Pound',
            'rate_to_ngn' => 1800, 'is_active' => true, 'sort_order' => 2,
        ]);

        $this->actingAs($this->admin())
            ->put(route('admin.currencies.update', $currency), [
                'symbol' => '£', 'name' => 'British Pound',
                'rate_to_ngn' => 1900, 'sort_order' => 2, 'is_active' => 1,
            ])
            ->assertRedirect();

        $this->assertSame('British Pound', $currency->fresh()->name);
        $this->assertEquals(1900, $currency->fresh()->rate_to_ngn);
    }
}
