<?php

namespace App\Http\Controllers;

use App\Models\Merchant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;

class BkashController extends Controller
{
    protected $isProduction = false;
    protected $sandbox_url = "https://checkout.sandbox.bka.sh/v1.2.0-beta/checkout/";
    protected $production_url = "https://checkout.pay.bka.sh/v1.2.0-beta/checkout/";
    protected $action_url = [
        'createURL' => 'payment/create',
        'executeURL' => 'payment/execute/',
        'tokenURL' => 'token/grant',
        'searchURL' => 'payment/search/'
    ];
    protected $merchant = null;
    protected $last_error = '';

    public function __construct(Merchant $merchant)
    {
        if (!$merchant) {
            throw new \Exception("Merchant information required", '500');
        }
        $this->isProduction = env("APP_ENV") == "production";
        $this->merchant = $merchant;
    }

    public function searchTransaction(string $trxid): array
    {
        $action = "SEARCH TRANSACTION";
        try {

            $creds = $this->loadCredentials();

            if (empty($creds)) { // Credentials error
                $this->lg('Credentials are not correct', 'warning', $action, 422, 'internal');
                $this->last_error = 'Credentials are not correct';
                return [];
            } else { // Credentials are correct

                // Get Token
                $token = $this->readToken();

                if (empty($token)) { // Token parsing error
                    $this->last_error = 'Cannot read grant token from server or filesystem';
                    $this->lg('Cannot read grant token from server or filesystem', 'warning',
                        $action, 422, 'internal');
                    return [];
                } else { // Token is correct

                    // Get payment information
                    $headers = [
                        'authorization' => $token,
                        'x-app-key' => isset($creds['app_key']) ? $creds['app_key'] : ''
                    ];

                    $url = $this->constructURL('searchURL', $trxid);
                    $data = json_decode($this->send($url, 'GET', [], $headers), true);
                    $this->lg("Search response from bKash PGW", 'info', $action, 200, 'internal', $data);

                    if (isset($data['trxID'])) {
                        $this->lg($data, 'info',
                            $action, 200, 'internal');
                        return $data;
                    } else {
                        $this->lg('Payment can not be found from cloud', 'warning',
                            $action, 404, 'internal');
                        $this->last_error = 'Payment can not be found from cloud';
                        return [];
                    }
                }
            }
        } catch (\Exception $e) {
            $this->lg($e, 'error', $action, 500, 'internal');
            $this->last_error = $e->getMessage();
            return [];
        }
    }


    public function getToken(): string
    {
        $action = "GET TOKEN";
        try {
            $merchant = $this->merchant;
            $creds = $this->loadCredentials();
            if (empty($creds)) {
                $this->lg("Credentials are invalid", 'alert', $action, 403, 'internal');
                return '';
            } else {

                $body = array(
                    'app_key' => $creds["app_key"],
                    'app_secret' => !empty($creds["app_secret"]) ? Crypt::decrypt($creds["app_secret"]) : ''
                );
                $header = array(
                    'Content-Type' => 'application/json',
                    'password' => !empty($creds["password"]) ? Crypt::decrypt($creds["password"]) : '',
                    'username' => $creds["username"]
                );

                $url = $this->constructURL('tokenURL');
                $resp = json_decode($this->send($url, 'POST', $body, $header), true);

                $this->lg("Token response from bKash PGW", 'info', $action, 200, 'internal', $resp);

                if (isset($resp['id_token'])) {
                    $expires_in = isset($resp['expires_in']) ? (int)$resp['expires_in'] : 0;
                    $time_to_expire = time() + $expires_in;
                    if (isset($merchant->id) && !empty($merchant->id)) {
                        $this->lg("Token fetched", 'info', $action, 200, 'internal');
                        // write in filesystem to track the token
                        $this->writeConfig([
                            'token' => $resp['id_token'],
                            'expires' => $time_to_expire
                        ], $merchant->id);

                    } else {
                        $this->lg("Merchant not available for token", 'warning', $action, 422, 'internal');
                        return '';
                    }
                    return $resp['id_token'];
                }
            }
            $this->lg("Unable to generate token", 'warning', $action, 422, 'internal');
            return '';
        } catch (\Exception $e) {
            $this->lg($e, 'error', $action, 500, 'internal');
        }
    }

    public function readToken(): string
    {
        $action = "READ TOKEN";
        $merchant = $this->merchant;
        if (isset($merchant->id) && !empty($merchant->id)) {
            $tokenData = $this->readConfig($merchant->id);
            if (isset($tokenData['token']) && !empty($tokenData['token'])) {
                $expires = isset($tokenData['expires']) ? $tokenData['expires'] : 0;
                if ($expires > time()) {
                    return $tokenData['token'];
                } else {
                    return $this->getToken();
                }
            } else {
                return $this->getToken();
            }
        }
        $this->lg("Could not read token", 'warning', $action, 422, 'internal');
        return "";
    }


    public function constructURL($action_url, $data = "")
    {
        $baseURL = $this->isProduction ? $this->production_url : $this->sandbox_url;
        return $baseURL . $this->action_url[$action_url] . $data;
    }

    public function loadCredentials(): array
    {
        if ($this->merchant && isset($this->merchant->app_key) && !empty($this->merchant->app_key)) {
            return [
                'app_key' => $this->merchant->app_key ?? '',
                'app_secret' => $this->merchant->app_secret ?? '',
                'username' => $this->merchant->bkash_username ?? '',
                'password' => $this->merchant->bkash_password ?? '',
            ];
        }
        return [];
    }


    public function send($url, $method = "GET", array $body = [], array $header = []): string
    {
        // dd($url);
        try {
            $client = new \GuzzleHttp\Client(['verify' => false]);
            $request = $client->request($method, $url, [
                'debug' => false,
                'json' => $body,
                'headers' => $header,
                'verify' => false,
            ]);
            return $request->getBody()->getContents();
        } catch (\Exception $e) {
            $this->lg($e, 'error', "SEND REQUEST TO bKASH", 500, 'internal');
            return $response = $e->getMessage();
        }
    }

    public function writeConfig(array $data, string $id): void
    {
        try {
            Redis::set('token:merchant:'.$id, json_encode($data));
            /*if (Storage::disk('local')->exists($id . '/bkash.txt')) {
                Storage::disk('local')->put($id . '/bkash.txt', json_encode($data));
            } else {
                Storage::makeDirectory($id);
                Storage::disk('local')->put($id . '/bkash.txt', json_encode($data));
            }*/
        } catch (\Exception $e){
            $this->lg($e, 'error', 'WRITE MERCHANT CONFIG', 500, 'internal');
        }
    }

    public function readConfig(string $id): array
    {
        try {
            $token = Redis::get('token:merchant:'.$id);
            return $token ?? [];
            /*if (Storage::disk('local')->exists($id . '/bkash.txt')) {
                return json_decode(Storage::disk('local')->get($id . '/bkash.txt'), true);
            } else {
                return [];
            }*/
        } catch (\Exception $e) {
            $this->lg($e, 'error', 'READ MERCHANT CONFIG', 500, 'internal');
            return [];
        }
    }

    public function getLastError() {
        return $this->last_error ?? '';
    }
}
