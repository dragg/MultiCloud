<?php namespace App\Services;

use App\Cloud;
use App\User;
use Illuminate\Support\Facades\Config;
use Google_Service_Oauth2;
use Google_Client;
use Illuminate\Support\Facades\Log;

class GoogleDriveService {

    public function create($attributes)
    {
        return $this->refreshOrCreate($attributes);
    }

    protected function refreshOrCreate($attributes)
    {
        $access_token = $attributes['access_token'];

        $client = $this->getClient($attributes);
        $authService = new Google_Service_Oauth2($client);
        $uid = $authService->userinfo->get()->id;
        $user = User::findOrFail($attributes['user_id']);

        $googleDrives = $user->clouds->where('type', Cloud::GoogleDrive);
        foreach($googleDrives as $drive) {
            if($drive->uid === $uid) {
                $drive->access_token = $access_token;
                $drive->save();
                return $drive;
            }
        }

        return Cloud::create(array_merge($attributes, ['uid' => $uid, 'type' => Cloud::GoogleDrive]));
    }


    public function getClient($attributes)
    {
        $client = new Google_Client();
        $client->setClientSecret(Config::get('clouds.google_drive.secret'));
        $client->setClientId(Config::get('clouds.google_drive.id'));

        if($attributes instanceof GoogleDrive) {
            $client->setAccessToken(json_encode([
                'access_token' => $attributes->access_token,
                'token_type' => $attributes->token_type,
                'expires_in' => $attributes->expires_in,
                'created' => $attributes->created
            ]));
        } else {
            $client->setAccessToken(json_encode([
                'access_token' => $attributes['access_token'],
                'token_type' => $attributes['token_type'],
                'expires_in' => $attributes['expires_in'],
                'created' => $attributes['created']
            ]));
        }


        return $client;
    }
}
