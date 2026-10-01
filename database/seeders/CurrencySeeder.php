<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CurrencySeeder extends Seeder
{
    public function run(): void
    {
        $currencies = [
            // Base currency — rate_to_ngn = 1 by definition
            ['code' => 'NGN', 'symbol' => '₦',  'name' => 'Nigerian Naira',      'rate_to_ngn' => 1.00,     'is_base' => true,  'is_active' => true, 'sort_order' => 1],
            // Major world currencies (rates are approximate — update via admin panel)
            ['code' => 'USD', 'symbol' => '$',   'name' => 'US Dollar',           'rate_to_ngn' => 1610.00,  'is_base' => false, 'is_active' => true, 'sort_order' => 2],
            ['code' => 'EUR', 'symbol' => '€',   'name' => 'Euro',                'rate_to_ngn' => 1750.00,  'is_base' => false, 'is_active' => true, 'sort_order' => 3],
            ['code' => 'GBP', 'symbol' => '£',   'name' => 'British Pound',       'rate_to_ngn' => 2050.00,  'is_base' => false, 'is_active' => true, 'sort_order' => 4],
            ['code' => 'CAD', 'symbol' => 'CA$', 'name' => 'Canadian Dollar',     'rate_to_ngn' => 1180.00,  'is_base' => false, 'is_active' => true, 'sort_order' => 5],
            ['code' => 'AUD', 'symbol' => 'A$',  'name' => 'Australian Dollar',   'rate_to_ngn' => 1040.00,  'is_base' => false, 'is_active' => true, 'sort_order' => 6],
            ['code' => 'GHS', 'symbol' => 'GH₵', 'name' => 'Ghanaian Cedi',      'rate_to_ngn' => 110.00,   'is_base' => false, 'is_active' => true, 'sort_order' => 7],
            ['code' => 'ZAR', 'symbol' => 'R',   'name' => 'South African Rand',  'rate_to_ngn' => 87.00,    'is_base' => false, 'is_active' => true, 'sort_order' => 8],
            ['code' => 'KES', 'symbol' => 'KSh', 'name' => 'Kenyan Shilling',     'rate_to_ngn' => 12.40,    'is_base' => false, 'is_active' => true, 'sort_order' => 9],
        ];

        foreach ($currencies as $currency) {
            DB::table('currencies')->updateOrInsert(
                ['code' => $currency['code']],
                array_merge($currency, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}
