<?php

namespace KLB\Themes\Database\Seeders;

use Illuminate\Database\Seeder;

class KLBDatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(AttributeFamilySeeder::class);
        $this->call(SizeAndColorAttributeModifications::class);
        $this->call(AttributeSwatchesSeeder::class);
        $this->call(CategoriesSeeder::class);
        $this->call(ProductsSeeder::class);

        // $this->call(ShopifyProductsSeeder::class);
    }
}