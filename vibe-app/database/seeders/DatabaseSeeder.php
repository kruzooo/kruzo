<?php

namespace Database\Seeders;

use App\Models\InventoryItem;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        InventoryItem::upsert([
            ['sku' => 'k-01-structural-boxy-tee', 'name' => 'K-01 Structural Boxy Tee', 'stock' => 18, 'low_stock_threshold' => 5, 'price' => 2250],
            ['sku' => 'brutalist-monolith-cuff-ring-set', 'name' => 'Brutalist Monolith Cuff Ring Set', 'stock' => 14, 'low_stock_threshold' => 5, 'price' => 3450],
            ['sku' => 'k-02-dropped-raglan-longline', 'name' => 'K-02 Dropped Raglan Longline', 'stock' => 12, 'low_stock_threshold' => 5, 'price' => 2650],
            ['sku' => 'geometric-carabiner-key-tether', 'name' => 'Geometric Carabiner Key Tether', 'stock' => 24, 'low_stock_threshold' => 6, 'price' => 1850],
            ['sku' => 'k-03-wide-leg-pleated-cargo', 'name' => 'K-03 Wide Leg Pleated Cargo', 'stock' => 10, 'low_stock_threshold' => 4, 'price' => 3850],
            ['sku' => 'atelier-heavyweight-tank', 'name' => 'Atelier Heavyweight Tank', 'stock' => 20, 'low_stock_threshold' => 5, 'price' => 1650],
            ['sku' => 'modular-crossbody-chest-rig', 'name' => 'Modular Crossbody Chest Rig', 'stock' => 9, 'low_stock_threshold' => 3, 'price' => 2950],
            ['sku' => 'k-04-sculpted-oversized-hoodie', 'name' => 'K-04 Sculpted Oversized Hoodie', 'stock' => 7, 'low_stock_threshold' => 3, 'price' => 3650],
            ['sku' => 'k-05-structural-raglan-crew', 'name' => 'K-05 Structural Raglan Crew', 'stock' => 15, 'low_stock_threshold' => 5, 'price' => 3250],
            ['sku' => 'k-06-washed-cargo-tee', 'name' => 'K-06 Washed Cargo Tee', 'stock' => 16, 'low_stock_threshold' => 5, 'price' => 2450],
            ['sku' => 'k-07-raw-cut-box-tee', 'name' => 'K-07 Raw Cut Box Tee', 'stock' => 8, 'low_stock_threshold' => 3, 'price' => 2250],
            ['sku' => 'k-08-sculpted-concrete-hoodie', 'name' => 'K-08 Sculpted Concrete Hoodie', 'stock' => 6, 'low_stock_threshold' => 3, 'price' => 3950],
            ['sku' => 'k-09-modular-field-cargo', 'name' => 'K-09 Modular Field Cargo', 'stock' => 11, 'low_stock_threshold' => 4, 'price' => 3850],
            ['sku' => 'k-10-wide-pleat-trouser', 'name' => 'K-10 Wide Pleat Trouser', 'stock' => 10, 'low_stock_threshold' => 4, 'price' => 3650],
            ['sku' => 'k-11-modular-chest-harness', 'name' => 'K-11 Modular Chest Harness', 'stock' => 5, 'low_stock_threshold' => 2, 'price' => 2950],
        ], ['sku'], ['name', 'low_stock_threshold', 'price']);

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}
