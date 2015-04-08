<?php namespace App\Services;

use App\DropBox;
use App\User;
use Dropbox as dbx;

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
            if($dropbox->uid === (string)$uid) {
                $dropbox->access_token = $access_token;
                $dropbox->save();
                return $dropbox;
            }
        }

        return DropBox::create(array_merge($attributes, ['uid' => $uid]));
    }
}
