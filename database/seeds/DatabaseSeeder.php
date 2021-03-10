<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(MerchantTableSeeder::class);
        $this->call(RolePermissionTableSeeder::class);
        $this->call(AdminTableSeeder::class);
    }
}
