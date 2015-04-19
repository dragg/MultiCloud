<?php namespace App\Services;

use App\DropBox;
use App\Cloud;
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

    public function getContents($cloudId, $path)
    {
        $cloud = Cloud::findOrFail((int)$cloudId);
        $contents = [];
        $client = new \Dropbox\Client($cloud->access_token, self::$clientIdentifier);

        $metadata = $client->getMetadataWithChildren($path);

        if($metadata['is_dir']) {
            foreach($metadata["contents"] as $content) {
                array_push($contents, [
                    'name' => $content['path'],
                    'is_dir' => $content['is_dir'],
                    'cloud_name' => $cloud->name,
                    'size' => $content['size'],
                    'modified' => $content['modified']
                ]);
            }
        } else {
            $contents = $client->createTemporaryDirectLink($path);
        }

        return $contents;
    }

    public function remove($cloudId, $path)
    {
        $cloud = Cloud::findOrFail((int)$cloudId);

        $client = new \Dropbox\Client($cloud->access_token, self::$clientIdentifier);

        $response = $client->delete($path);

        return $response;
    }

    public function move($cloudId, $path, $newPath)
    {
        $cloud = Cloud::findOrFail((int)$cloudId);

        $client = new \Dropbox\Client($cloud->access_token, self::$clientIdentifier);

        $response = $client->move($path, $newPath);

        return $response;
    }
}
