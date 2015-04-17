<?php namespace App\Services;

use App\DropBox;
use App\Cloud;
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

        $dropBoxes = $user->clouds->where('type', Cloud::DropBox);
        foreach ($dropBoxes as $dropbox) {
            if($dropbox->uid === (string)$uid) {
                $dropbox->access_token = $access_token;
                $dropbox->save();
                return $dropbox;
            }
        }

        return Cloud::create(array_merge($attributes, ['uid' => $uid, 'type' => Cloud::DropBox]));
    }

    public function getContents($path, $access_token)
    {
        $contents = [];
        $client = new \Dropbox\Client($access_token, self::$clientIdentifier);

        $metadata = $client->getMetadataWithChildren($path);
        foreach($metadata["contents"] as $content) {
            array_push($contents, [$content['path'], $content['is_dir']]);
        }
        return $contents;
    }
}
