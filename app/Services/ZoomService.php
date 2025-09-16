<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class ZoomService
{
   protected $accountId;
   protected $clientId;
   protected $clientSecret;
   protected $defaultUser;

   public function __construct()
   {
      $this->accountId = config('services.zoom.account_id');
      $this->clientId = config('services.zoom.client_id');
      $this->clientSecret = config('services.zoom.client_secret');
      $this->defaultUser = config('services.zoom.default_user');
   }

   // get S2S token (cached)
   public function getAccessToken()
   {
      if (Cache::has('zoom_s2s_token')) {
         return Cache::get('zoom_s2s_token');
      }

      $basic = base64_encode($this->clientId . ':' . $this->clientSecret);

      $resp = Http::withHeaders([
         'Authorization' => "Basic {$basic}",
      ])->asForm()->post('https://zoom.us/oauth/token', [
               'grant_type' => 'account_credentials',
               'account_id' => $this->accountId,
            ]);

      $resp->throw();
      $data = $resp->json();

      $token = $data['access_token'];
      $expiresIn = $data['expires_in'] ?? 3500;

      Cache::put('zoom_s2s_token', $token, $expiresIn - 60);

      return $token;
   }

   // create scheduled meeting for $userIdOrEmail
   public function createMeeting($userIdOrEmail, array $payload)
   {
      $token = $this->getAccessToken();
      $resp = Http::withToken($token)
         ->post("https://api.zoom.us/v2/users/{$userIdOrEmail}/meetings", $payload);
      $resp->throw();
      return $resp->json();
   }
}
