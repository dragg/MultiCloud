<?php namespace App\Services;

use App\Cloud;
use App\User;
use Dropbox as dbx;

class DropBoxService extends CloudService {

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
        $cloud = $this->getCloud($cloudId);
        $client = $this->getClient($cloudId);

        $contents = [];

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

    public function removeContent($cloudId, $path)
    {
        $client = $this->getClient($cloudId);

        $response = $client->delete($path);

        return $response;
    }

    public function moveContent($cloudId, $path, $newPath)
    {
        $client = $this->getClient($cloudId);

        $response = $client->move($path, $newPath);

        return $response;
    }

    public function infoCloud($cloudId)
    {
        $client = $this->getClient($cloudId);

        $response = $client->getAccountInfo();

        return $response;
    }

    public function removeCloud($cloudId)
    {
        $cloud = $this->getCloud($cloudId);
        $client = $this->getClient($cloudId);

        $client->disableAccessToken();
        return $cloud->delete();
    }

    private function getClient($cloudId)
    {
        $cloud = $this->getCloud($cloudId);

        $client = new \Dropbox\Client($cloud->access_token, self::$clientIdentifier);

        return $client;
    }

    public function shareStart($cloudId, $path)
    {
        $client = $this->getClient($cloudId);

        return $client->createShareableLink($path);
    }

    public function renameContent($cloudId, $path, $newPath)
    {
        $client = $this->getClient($cloudId);

        $response = $client->move($path, $newPath);

        return $response;
    }

    public function copyContent($cloudId, $path, $newPath)
    {
        $client = $this->getClient($cloudId);

        $response = $client->copy($path, $newPath);

        return $response;
    }
}
