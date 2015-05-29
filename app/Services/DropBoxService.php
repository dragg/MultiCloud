<?php namespace App\Services;

use App\Cloud;
use App\User;
use Dropbox as dbx;
use \Dropbox\Client as DropboxClient;

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

        $client = new DropboxClient($cloud->access_token, self::$clientIdentifier);

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

    public function copyToSameCloud($cloudIdFrom, $cloudIdTo, $pathFrom, $pathTo)
    {
        $cloudFrom = $this->getClient($cloudIdFrom);
        $cloudTo = $this->getClient($cloudIdTo);

        $copyRef = $cloudFrom->createCopyRef($pathFrom);
        $cloudTo->copyFromCopyRef($copyRef, $pathTo);
    }

    public function downloadContents($cloudId, $cloudPath, $path)
    {
        $client = $this->getClient($cloudId);

        //Create dir
        $folder = storage_path() . '/app' . $path;


        $metadata = $client->getMetadataWithChildren($cloudPath);
        $path = $folder . $metadata["path"];
        if($metadata['is_dir']) {
            $this->downloadDir($cloudPath, $folder, $client);
        } else {
            $this->downloadFile($cloudPath, $path, $client);
        }
    }

    private function downloadFile($cloudPath, $localPath, DropboxClient $client)
    {
        $fd = fopen($localPath, "wb");
        $client->getFile($cloudPath, $fd);
        fclose($fd);
    }

    private function downloadDir($cloudPath, $localPath, $client)
    {
        //Create dir
        mkdir($localPath . $cloudPath);

        //Then download files
        $metadata = $client->getMetadataWithChildren($cloudPath);
        foreach($metadata["contents"] as $content) {
            if($content["is_dir"]) {
                $this->downloadDir($content["path"], $localPath, $client);
            } else {
                $path = $localPath . $content["path"];
                $this->downloadFile($content["path"], $path, $client);
            }
        }
    }

    public function uploadContents($cloudId, $cloudPath, $path)
    {
        $client = $this->getClient($cloudId);

        //Get full path
        $path = $folder = storage_path() . '/app' . $path;

        //Get contents
        $contents = $this->getLocalContent($path);

        foreach($contents as $content) {
            $contentPath = $path . '/' . $content;
            if(is_dir($contentPath)) {
                $folderCloudPath = $cloudPath . '/' . $content;
                $client->createFolder($folderCloudPath);
                $this->uploadDir($folderCloudPath, $contentPath, $client);
            }
            else {
                $this->uploadFile($cloudPath . '/' . $content, $contentPath, $client);
            }
        }
    }

    private function uploadDir($cloudPath, $localPath, DropboxClient $client)
    {
        //Get contents
        $contents = $this->getLocalContent($localPath);

        foreach($contents as $content) {
            $contentPath = $localPath . '/' . $content;
            if(is_dir($contentPath)) {
                $folderCloudPath = $cloudPath . '/' . $content;
                $client->createFolder($folderCloudPath);
                $this->uploadDir($folderCloudPath, $contentPath, $client);
            }
            else {
                $this->uploadFile($cloudPath . '/' . $content, $contentPath, $client);
            }
        }
    }

    private function uploadFile($cloudPath, $localPath, DropboxClient $client)
    {
        $fd = fopen($localPath, "rb");
        $client->uploadFile($cloudPath, dbx\WriteMode::add(), $fd, filesize($localPath));
        fclose($fd);
    }
}
