<?php

namespace Tests\Feature;

use App\Models\Currency;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Tests\TestCase;

/**
 * Public currency switcher (CurrencyController).
 *
 *   POST /currency/set  → stores an ALLOWED (active) currency in the session.
 *   GET  /currencies    → lists active currencies as JSON for the switcher.
 */
class CurrencySwitchTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(ThrottleRequests::class);
    }

    private function makeCurrency(string $code, array $overrides = []): Currency
    {
        return Currency::create(array_merge([
            'code'        => $code,
            'symbol'      => $code,
            'name'        => $code . ' Currency',
            'rate_to_ngn' => 0.001,
            'is_base'     => false,
            'is_active'   => true,
            'sort_order'  => 0,
        ], $overrides));
    }

    /** @test */
    public function it_stores_a_valid_active_currency_in_the_session()
    {
        $this->makeCurrency('USD');

        $res = $this->postJson('/currency/set', ['code' => 'usd']); // lower-case → upcased

        $res->assertOk()->assertJson(['ok' => true, 'currency' => 'USD']);
        $this->assertSame('USD', session('currency'));
    }

    /** @test */
    public function it_ignores_an_unknown_currency_and_keeps_the_default()
    {
        $res = $this->postJson('/currency/set', ['code' => 'XYZ']);

        $res->assertOk()->assertJson(['ok' => true, 'currency' => 'NGN']);
        $this->assertNull(session('currency'));
    }

    /** @test */
    public function it_ignores_an_inactive_currency()
    {
        $this->makeCurrency('GBP', ['is_active' => false]);

        $res = $this->postJson('/currency/set', ['code' => 'GBP']);

        $res->assertJson(['currency' => 'NGN']);
        $this->assertNull(session('currency'));
    }

    /** @test */
    public function the_public_currencies_endpoint_lists_active_currencies()
    {
        $this->makeCurrency('USD');
        $this->makeCurrency('EUR', ['is_active' => false]);

        $res = $this->getJson('/currencies');

        $res->assertOk();
        $codes = collect($res->json())->pluck('code')->all();
        $this->assertContains('USD', $codes);
        $this->assertNotContains('EUR', $codes);
    }
}
