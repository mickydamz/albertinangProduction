<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\StoreLocation;

class StoreLocationSeeder extends Seeder
{
    public function run()
    {
        $stores = [
            [
                'name'       => 'Enugu Showroom (HQ)',
                'address'    => "17-18 Zik's Avenue, Uwani, Enugu 400105",
                'phone'      => '+234 806 406 6170',
                'email'      => 'Info@Albertinang.com',
                'hours'      => 'Mon–Sat: 8:00 AM – 6:00 PM · Sun: Closed',
                'is_hq'      => true,
                'sort_order' => 1,
            ],
            [
                'name'       => 'Lagos Showroom',
                'address'    => '26 Lawanson Road, Surulere, Lagos',
                'phone'      => '+234 806 406 6170',
                'email'      => 'Info@Albertinang.com',
                'hours'      => 'Mon–Sat: 8:00 AM – 6:00 PM · Sun: Closed',
                'is_hq'      => false,
                'sort_order' => 2,
            ],
            [
                'name'       => 'Awka Showroom',
                'address'    => 'Enugu-Onitsha Expressway, Awka (near Unizik)',
                'phone'      => '+234 806 406 6170',
                'email'      => 'Info@Albertinang.com',
                'hours'      => 'Mon–Sat: 8:00 AM – 6:00 PM · Sun: Closed',
                'is_hq'      => false,
                'sort_order' => 3,
            ],
        ];

        foreach ($stores as $store) {
            StoreLocation::firstOrCreate(
                ['name' => $store['name']],
                $store
            );
        }
    }
}
