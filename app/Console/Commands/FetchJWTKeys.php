<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class FetchJWTKeys extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jwtkeys:fetch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetching JWT keys from S3';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            $s3_file = Storage::disk('s3')->get("oauth-private.key");
            if ($s3_file) {
                $local = Storage::disk('storage');
                $local->put("./oauth-private.key", $s3_file);
                echo "Oauth private keys successfully stored";
            } else {
                echo "Oauth private file not found in S3";
            }


            $s3_file = Storage::disk('s3')->get("oauth-public.key");
            if ($s3_file) {
                $local = Storage::disk('storage');
                $local->put("./oauth-public.key", $s3_file);
                echo "Oauth public keys successfully stored";
            } else {
                echo "Oauth public file not found in S3";
            }

            echo "Key fetched successfully";
        } catch (\Exception $e){
            echo $e->getMessage();
        }
    }
}
