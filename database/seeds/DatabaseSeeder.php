<?php

use Illuminate\Database\Seeder;
use Webkul\Velocity\Database\Seeders\VelocityMetaDataSeeder;
use Webkul\Admin\Database\Seeders\DatabaseSeeder as BagistoDatabaseSeeder;
use KLB\Themes\Database\Seeders\KLBDatabaseSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(BagistoDatabaseSeeder::class);
        $this->call(VelocityMetaDataSeeder::class);
        $this->call(KLBDatabaseSeeder::class);
    }
}
