<?php namespace App\Services;

use App\Dropbox;
use App\User;
use Dropbox as dbx;
use Illuminate\Support\Facades\Log;

class DropBoxServices {

    private static $clientIdentifier = "MultiCloudThesis alpha";

    public function create($attributes)
    {
        return $this->refreshOrCreate($attributes);
    }

    protected function refreshOrCreate($attributes)
    {
        $access_token = $attributes['access_token'];

        $client = new dbx\Client($access_token, self::$clientIdentifier);
        $uid = $client->getAccountInfo()['uid'];
        $user = User::findOrFail($attributes['user_id']);

        foreach ($user->dropboxes as $dropbox) {
            if($dropbox->uid === $uid) {
                $dropbox->access_token = $access_token;
                $dropbox->save();
                return $dropbox;
            }
        }

        return Dropbox::create(array_merge($attributes, ['uid' => $uid]));
    }
}