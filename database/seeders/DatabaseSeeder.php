<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call(CurrencySeeder::class);

        // Insert admin user
        $adminEmail = 'kd@gmail.com';
        $hashedPassword = Hash::make('12345678');

        DB::table('users')->insert([
            'name' => 'Admin',
            'email' => $adminEmail,
            'password' => $hashedPassword,
            'created_at' => now(),
            'updated_at' => now(),
            'role' => 'admin', // Assuming role is a field in users table
        ]);

        // Insert supplier user
        $supplierEmail = 'jio@gmail.com';
        $supplierPassword = Hash::make('12345678');

        DB::table('users')->insert([
            'name' => 'Supplier Jio',
            'email' => $supplierEmail,
            'password' => $supplierPassword,
            'created_at' => now(),
            'updated_at' => now(),
            'role' => 'supplier', // Assuming role is a field in users table
        ]);

        // Insert countries
        $countries = [
            ['name' => 'United States', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Canada', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'United Kingdom', 'created_at' => now(), 'updated_at' => now()],
            // Add other countries as needed
        ];

        DB::table('countries')->insert($countries);

        // Get the country ID for the United States
        $usCountryId = DB::table('countries')->where('name', 'United States')->value('id');

        // Insert payment methods
        $paymentMethods = [
            ['name' => 'Bank', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Credit Card', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Crypto', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('payment_methods')->insert($paymentMethods);

        // Insert bank accounts with country_id
        DB::table('bank_accounts')->insert([
            [
                'name' => 'Bank of America', 
                'account_number' => '1234567890', 
                'swift_code' => 'BOFAUS3N', 
                'address' => '123 Main St, Anytown, USA', 
                'country_id' => $usCountryId, // Add country_id
                'country' => 'United States', // Add country_id
                'is_active' => true, 
                'created_at' => now(), 
                'updated_at' => now()
            ],
            [
                'name' => 'Chase Bank', 
                'account_number' => '0987654321', 
                'swift_code' => 'CHASUS33', 
                'address' => '456 Elm St, Anytown, USA', 
                'country_id' => $usCountryId, // Add country_id
                'country' => 'United States', // Add country_id
                'is_active' => true, 
                'created_at' => now(), 
                'updated_at' => now()
            ],
        ]);

        // Insert products for the supplier
        $supplierId = DB::table('users')->where('email', $supplierEmail)->value('id'); // Get the supplier's ID

        for ($i = 1; $i <= 10; $i++) {
            DB::table('products')->insert([
                'name' => 'Product ' . $i,
                'stock' => 100,
                'price' => 100,
                'supplier_id' => $supplierId, // Associate product with the supplier
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
