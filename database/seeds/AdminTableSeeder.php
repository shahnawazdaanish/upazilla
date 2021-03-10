<?php

use Illuminate\Database\Seeder;

class AdminTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Admin::query()->create([
            'username' => 'admin',
            'name' => 'Administrator',
            'password' => bcrypt('123456'),
        ])->assignRole('Super Admin');
    }
}
