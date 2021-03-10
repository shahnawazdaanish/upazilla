<?php

use Illuminate\Database\Seeder;
use App\Models\Merchant;

class MerchantTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Merchant::query()->create([
            'name' => 'Unilever Bangladesh Limited',
            'slug' => 'unilever-pureit',
            'contact' => '01747544555',
            'account_no' => '50011',
            'app_key' => '5tunt4masn6pv2hnvte1sb5n3j',
            'app_secret' => \Illuminate\Support\Facades\Crypt::encrypt('1vggbqd4hqk9g96o9rrrp2jftvek578v7d2bnerim12a87dbrrka'),
            'bkash_username' => 'sandboxTestUser',
            'bkash_password' => \Illuminate\Support\Facades\Crypt::encrypt('hWD@8vtzw0')
        ]);
    }
}
