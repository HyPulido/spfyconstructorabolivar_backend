<?php

namespace App\Http\Controllers;

use App\Services\SpotifyService;
use App\Traits\BaseApp;

class SpotifyController extends Controller
{
    use BaseApp;

    public function generateTokenByAuthCode($code)
    {
        $error_code = "SYGTBAC200";
        $data = null;
        try {

            $spotifyService = new SpotifyService();
            $generateTokenByAuthCodeResponse = $spotifyService->generateTokenByAuthCode($code);

            if (isset($generateTokenByAuthCodeResponse['error'])) {
                $error_code = "SYGTBAC400";
                $data['error'] = $generateTokenByAuthCodeResponse;
            } else {
                $data['response'] = $generateTokenByAuthCodeResponse;
            }
        } catch (\Exception $e) {
            $error_code = "MEGM500";
            $data['error'] = $this->getException($e);
        }

        return $this->setCustomizeResponse(array('error_code' => $error_code, 'data' => $data, 'function' => __FUNCTION__, 'class' => __CLASS__));
    }




    public function oAuth()
    {
        $state = $this->generateRandomString(16);
        $scope = 'user-read-private user-read-email';
        $clientId = getenv('SPOTIFY_CLIENT_ID'); // Tu ID de cliente de Spotify
        $redirectUri = 'http://localhost:3000/callback'; // Tu URI de redirección

        $spotifyAuthUrl = 'https://accounts.spotify.com/authorize?' . http_build_query([
            'response_type' => 'code',
            'client_id' => $clientId,
            'scope' => $scope,
            'redirect_uri' => $redirectUri,
            'state' => $state,
        ]);

        return response()->json(['spotify_auth_url' => $spotifyAuthUrl]);
    }

    private function generateRandomString($length)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}
