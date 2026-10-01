<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CryptoAddress; 

class CryptoAddressesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
 

public function run()
{
    CryptoAddress::create(['currency' => 'BTC', 'address' => 'YourBitcoinAddressHere']);
    CryptoAddress::create(['currency' => 'ETH', 'address' => 'YourEthereumAddressHere']);
    CryptoAddress::create(['currency' => 'USDT', 'address' => 'YourTetherAddressHere']);
}
}
