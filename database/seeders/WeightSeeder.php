<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Database\Seeder;

class WeightSeeder extends Seeder
{
    /*
    |--------------------------------------------------------------------------
    | Logical weight reasoning
    |--------------------------------------------------------------------------
    | Category weights = the typical/median unit weight for products in that
    | group. Subcategory weights are more precise; they override the category.
    | Source: real-world appliance specifications for Nigerian market products.
    |--------------------------------------------------------------------------
    */

    public function run(): void
    {
        $this->seedCategories();
        $this->seedSubcategories();
    }

    // ── Category weights (broad estimate — subcategory overrides take priority) ─

    private function seedCategories(): void
    {
        $weights = [
            // Large cooling/refrigeration units — average ~60 kg
            'Refrigerators & Freezers'    => 60.0,
            'Refrigeration'               => 60.0,

            // AC units — split AC is the common sale, ~25 kg
            'Air Conditioners'            => 25.0,
            'Air Conditioning'            => 25.0,

            // Generators & power backup — generator is the heavy item
            'Generators & Power'          => 40.0,
            'Power'                       => 40.0,
            'Power Backup'                => 20.0,

            // Washing & laundry — standard machine ~50 kg
            'Washing & Laundry'           => 50.0,
            'Laundry'                     => 50.0,

            // Fans — standing fan is the typical unit, ~8 kg
            'Fans'                        => 8.0,
            'Cooling Fans'                => 8.0,

            // Cooking — gas cooker is the anchor product, ~25 kg
            'Cooking Appliances'          => 25.0,
            'Cooking'                     => 25.0,

            // Kitchen small appliances — blenders, mixers etc., ~5 kg
            'Kitchen Appliances'          => 5.0,
            'Small Kitchen Appliances'    => 5.0,

            // Entertainment — TV is the heaviest common item, ~15 kg
            'Entertainment'               => 15.0,
            'TVs & Audio'                 => 15.0,
            'Television & Audio'          => 15.0,

            // Home care / pressing — iron is light, ~2 kg
            'Home Care'                   => 2.0,
            'Laundry & Home Care'         => 2.0,

            // Electrical accessories — sockets, cables, very light
            'Electrical Accessories'      => 0.5,
            'Accessories'                 => 1.0,

            // General equipment buckets
            'Home Equipment'              => 10.0,
            'Home Equipments'             => 10.0,
            'Office Equipment'            => 10.0,
            'Office Equipments'           => 10.0,
            'Outdoor Equipment'           => 15.0,
            'Outdoor Equipments'          => 15.0,
            'Food Service Equipment'      => 25.0,
            'Industrial Equipment'        => 30.0,

            // Water
            'Water Dispensers'            => 15.0,
            'Water & Dispensers'          => 15.0,
        ];

        foreach ($weights as $name => $kg) {
            Category::where('name', $name)->update(['estimated_weight_kg' => $kg]);
        }
    }

    // ── Subcategory weights (precise per product type) ─────────────────────────

    private function seedSubcategories(): void
    {
        $weights = [

            // ── Air Conditioners ──────────────────────────────────────────────
            'AC Guards'            => 0.5,   // thin metal/plastic guard panel
            'Adapters'             => 0.2,   // small plug adapters
            'AC Coupling Kit'      => 1.0,   // piping + fittings kit
            'Split AC'             => 25.0,  // indoor + outdoor unit combined
            'Floor Standing AC'    => 80.0,  // cassette/floor unit, very heavy

            // ── Electrical Accessories ────────────────────────────────────────
            'Extensions'           => 0.5,   // extension cords/boards
            'Small Sockets'        => 0.2,   // individual socket adapters
            'Surge Protector'      => 0.5,   // compact surge strip

            // ── Washing & Laundry ─────────────────────────────────────────────
            'Washing Machines'          => 50.0,  // top/front-load domestic machine
            'Industrial Washing Machine'=> 80.0,  // commercial-grade machine
            'Dryer'                     => 45.0,  // tumble dryer unit

            // ── Entertainment / Audio-Visual ──────────────────────────────────
            'Televisions'          => 15.0,  // 43-55" LED/OLED TV
            'Sound Bar'            => 5.0,   // standalone soundbar
            'Home Theaters'        => 8.0,   // receiver + speaker set
            'Wireless Speaker'     => 2.0,   // portable Bluetooth speaker
            'TV Stand'             => 15.0,  // flat-pack TV console
            'Wall Shelf'           => 5.0,   // bracket/floating shelf

            // ── Fans ──────────────────────────────────────────────────────────
            'Standing Fans'        => 8.0,   // pedestal fan with base
            'Ceiling Fan'          => 5.0,   // ceiling-mount fan + motor
            'Wall Fan'             => 3.0,   // wall-bracket fan
            'Rechargeable Fan'     => 3.0,   // battery-powered desk/stand fan
            'Heavy Duty Fan'       => 15.0,  // industrial drum/wall fan
            'Orbit Fan'            => 4.0,   // oscillating desk/table fan
            'Tower Fan'            => 7.0,   // tall column fan
            'Bladeless Fan'        => 5.0,   // Dyson-style bladeless tower
            'Air Cooler'           => 12.0,  // evaporative cooler with water tank

            // ── Power & Energy ────────────────────────────────────────────────
            'Generators'           => 80.0,  // petrol/gas generator set
            'Half Engine'          => 30.0,  // open-frame generator engine only
            'Generator Oil'        => 10.0,  // 4-litre oil containers (per case)
            'Batteries'            => 15.0,  // 100Ah deep-cycle battery
            'Inverters'            => 10.0,  // 1–2 kVA inverter unit
            'UPS'                  => 10.0,  // uninterruptible power supply
            'Solar Panel'          => 20.0,  // 200–400W rigid panel
            'Stabilizers'         => 15.0,  // automatic voltage regulator

            // ── Refrigeration ─────────────────────────────────────────────────
            'Refrigerator'             => 60.0,  // 200–300L upright fridge
            'Chest Freezers'           => 60.0,  // 200–300L chest freezer
            'Deep Freezer'             => 65.0,  // larger 300–400L chest freezer
            'Beverage Cooler'          => 40.0,  // glass-door display cooler
            'Beverage Cooler / Showcase' => 40.0,
            'Side By Side Refrigerator'  => 90.0, // large 500L+ side-by-side

            // ── Water ─────────────────────────────────────────────────────────
            'Water Dispensers'     => 15.0,  // floor-standing hot/cold dispenser

            // ── Cooking ───────────────────────────────────────────────────────
            'Gas Cookers'          => 25.0,  // 4-burner table-top gas cooker
            'Standing Gas Cooker'  => 35.0,  // 4-burner oven cooker with legs
            'Table Gas Cooker'     => 15.0,  // 2-burner compact table cooker
            'Gas Hobs'             => 10.0,  // built-in hob only (no oven)
            'Microwave Cooker'     => 15.0,  // 20–30L countertop microwave

            // ── Kitchen Small Appliances ──────────────────────────────────────
            'Juicers'              => 3.0,   // centrifugal/cold-press juicer
            'Blenders'             => 3.0,   // domestic countertop blender
            'Industrial Blender'   => 20.0,  // commercial high-speed blender
            'Food Mixer'           => 8.0,   // stand mixer (KitchenAid style)
            'Cake Mixer'           => 8.0,   // tilt-head or bowl-lift mixer
            'Industrial Dough Mixer' => 30.0, // 10–20L commercial spiral mixer
            'Industrial Ice Cream Machine' => 40.0, // batch freezer / soft-serve machine
            'Air Fryers'           => 8.0,   // 5–7L countertop air fryer
            'Dish Washer'          => 40.0,  // under-counter dishwasher

            // ── Home Care / Ironing ───────────────────────────────────────────
            'Electric Iron'        => 2.0,   // steam/dry iron
            'Detergent'            => 1.0,   // per-unit pack weight (detergent sachet/bottle)

            // ── General equipment buckets ─────────────────────────────────────
            'Home Equipments'      => 10.0,
            'Home Equipment'       => 10.0,
            'Office Equipment'     => 10.0,
            'Office Equipments'    => 10.0,
            'Outdoor Equipment'    => 15.0,
            'Outdoor Equipments'   => 15.0,
            'Food Service Equipment' => 25.0,
        ];

        foreach ($weights as $name => $kg) {
            Subcategory::where('name', $name)->update(['estimated_weight_kg' => $kg]);
        }
    }
}
