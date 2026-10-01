<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * Smoke test: every admin index page that got the search/pagination treatment
 * must still render (no Blade/controller errors), both plain and with a search
 * term applied.
 */
class AdminIndexPagesSmokeTest extends TestCase
{
    use DatabaseTransactions;

    private function admin(): User
    {
        $user = User::factory()->create();
        $user->role = 'admin';
        $user->save();
        return $user;
    }

    public function pages(): array
    {
        return [
            'tags'            => ['admin.tags.index'],
            'colors'          => ['admin.colors.index'],
            'sizes'           => ['admin.sizes.index'],
            'faqs'            => ['admin.faqs.index'],
            'banners'         => ['admin.banners.index'],
            'currencies'      => ['admin.currencies.index'],
            'brands'          => ['admin.brands.index'],
            'states'          => ['admin.states.index'],
            'locations'       => ['admin.locations.index'],
            'pickup-points'   => ['admin.pickup-points.index'],
            'store-locations' => ['admin.store-locations.index'],
            'returns'         => ['admin.returns.index'],
            'cancellations'   => ['admin.cancellations.index'],
            'coupons'         => ['admin.coupons.index'],
            'categories'      => ['admin.categories.index'],
            'settings'        => ['admin.settings.index'],
            'products'        => ['admin.products.index'],
            'reviews'         => ['admin.reviews.index'],
            'paystack-txns'   => ['admin.paystack-transactions.index'],
            'shipping'        => ['admin.shipping.index'],
            'invoice-settings'=> ['admin.invoice.settings'],
        ];
    }

    /**
     * @test
     * @dataProvider pages
     */
    public function admin_index_page_renders(string $route)
    {
        $admin = $this->admin();

        $this->actingAs($admin)->get(route($route))->assertOk();
        // With a search term the query path must also render cleanly.
        $this->actingAs($admin)->get(route($route, ['search' => 'zzz']))->assertOk();
    }
}
