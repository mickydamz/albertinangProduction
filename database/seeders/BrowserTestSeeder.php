<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\City;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use App\Models\State;
use App\Models\Location;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;

class BrowserTestSeeder extends Seeder
{
    public function run(): void
    {
        if (!app()->environment('testing') || !str_ends_with(config('database.connections.mysql.database'), '_test')) {
            throw new \RuntimeException('Browser fixtures require an isolated testing database.');
        }
        $countries = [
            'Nigeria' => ['NGA', ['Abia','Adamawa','Akwa Ibom','Anambra','Bauchi','Bayelsa','Benue','Borno','Cross River','Delta','Ebonyi','Edo','Ekiti','Enugu','Federal Capital Territory','Gombe','Imo','Jigawa','Kaduna','Kano','Katsina','Kebbi','Kogi','Kwara','Lagos','Nasarawa','Niger','Ogun','Ondo','Osun','Oyo','Plateau','Rivers','Sokoto','Taraba','Yobe','Zamfara']],
            'Algeria' => ['DZA', ['Adrar','Chlef','Laghouat','Oum El Bouaghi','Batna','Bejaia','Biskra','Bechar','Blida','Bouira','Tamanrasset','Tebessa','Tlemcen','Tiaret','Tizi Ouzou']],
            'United States' => ['USA', ['Alabama','Alaska','Arizona','Arkansas','California','Colorado','Connecticut','Delaware','Florida','Georgia','Hawaii','Idaho','Illinois','Indiana','Iowa','Kansas','Kentucky','Louisiana','Maine','Maryland','Massachusetts','Michigan','Minnesota','Mississippi','Missouri','Montana','Nebraska','Nevada','New Hampshire','New Jersey','New Mexico','New York','North Carolina','North Dakota','Ohio','Oklahoma','Oregon','Pennsylvania','Rhode Island','South Carolina','South Dakota','Tennessee','Texas','Utah','Vermont','Virginia','Washington','West Virginia','Wisconsin','Wyoming']],
        ];
        foreach (['Canada','Australia','Ghana','France','Germany','United Kingdom','Kenya','South Africa','India','Italy'] as $name) {
            $countries[$name] = [null, [$name . ' Test Region']];
        }
        foreach ($countries as $name => [$iso, $states]) {
            $country = Country::firstOrCreate(['name' => $name], ['iso_code' => $iso]);
            foreach ($states as $state) City::firstOrCreate(['country_id' => $country->id, 'name' => $state]);
        }
        $this->call(CurrencySeeder::class);
        foreach (['customer', 'other', 'admin'] as $role) {
            User::updateOrCreate(['email' => $role.'@browser.test'], ['name' => ucfirst($role).' Browser', 'password' => Hash::make('BrowserTest123!'), 'role' => $role === 'admin' ? 'admin' : 'user', 'email_verified_at' => now()]);
        }
        $category = Category::firstOrCreate(['name' => 'Browser Appliances'], ['is_active' => true]);
        Product::updateOrCreate(['sku' => 'BROWSER-001'], ['name' => 'Browser Test Microwave', 'price' => 50000, 'stock' => 100, 'is_active' => true, 'category_id' => $category->id, 'installation_options' => [['label' => 'Install', 'price' => 1500]]]);
        $state = State::firstOrCreate(['name' => 'Browser Lagos'], ['is_active' => true]);
        Location::updateOrCreate(['name' => 'Browser Ikeja'], ['state_id' => $state->id, 'is_active' => true, 'shipping_cost' => 2500, 'truck_shipping_cost' => 5000]);
        Cache::flush();
    }
}
