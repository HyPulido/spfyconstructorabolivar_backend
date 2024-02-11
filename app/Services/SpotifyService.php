<?php

namespace App\Services;

use App\Services\LogsServices;
use App\Traits\BaseApp;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Log;

class SpotifyService
{

    use BaseApp;
    use LogsServices;

    protected $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    public function generateTokenByAuthCode($code)
    {
        try {
            $start = Carbon::now();
            $url = getenv('SPOTIFY_ENDPOINT') . "/api/token";
            $api = "Generate Token By Authorization Code";

            // echo $code;
            // // $client = new Client();
            // $headers = [
            //     'Content-Type' => 'application/x-www-form-urlencoded',
            //     'Authorization' => 'Basic ZmUyZDhjYTM2Y2VhNGFlMTg3NGY1MWUzOGZkMzVhZTU6Yzg3NThkMTFmYjcyNDYxMjgyNTViMWM2NTA2ZGY5ODU=',
            // ];
            // $options = [
            //     'form_params' => [
            //         'grant_type' => 'authorization_code',
            //         'code' => $code,
            //         'redirect_uri' => 'http://localhost:3000/login/callback'
            //     ]
            // ];
            // $request = new Request('POST', 'https://accounts.spotify.com/api/token', $headers);
            // $response = $this->client->send($request, $options);
            // print_r($response->getBody());
            // die();
            // echo "\n".json_encode($response->getBody());


            $curl = curl_init();

            curl_setopt_array($curl, $request=array(
                CURLOPT_URL => 'https://accounts.spotify.com/api/token',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => 'grant_type=authorization_code&code='.$code.'&redirect_uri='.getenv('SPOTIFY_REDIRECT_URL'),
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/x-www-form-urlencoded',
                    'Authorization: Basic '.base64_encode(getenv('SPOTIFY_CLIENT_ID').':'.getenv('SPOTIFY_CLIENT_SECRET')),
               ),
            ));


            $response = curl_exec($curl);

            curl_close($curl);
            return json_decode($response, true);


            // $request = new Request('POST', $url);
            // $response = $this->client->send($request);
            // $result = $this->processService($api, $request, $response, $start);
            // return json_decode($result, true);
        } catch (Exception $e) {
            $data = $this->getException($e);
            Log::channel("error_provider")->alert(json_encode($data));
        }
    }
}
