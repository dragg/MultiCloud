<?php namespace App\Services;

use App\Cloud;
use App\User;
use Yandex\Disk\DiskClient as YandexDiskClient;

class YandexDiskService extends CloudService {

    const
        FILE = "file",
        FOLDER = "dir";

    public function create($attributes)
    {
        return $this->refreshOrCreate($attributes);
    }

    protected function refreshOrCreate($attributes)
    {
        $access_token = $attributes['access_token'];

        $diskClient = new YandexDiskClient($access_token);
        list($uid) = explode("\n", $diskClient->getLogin());

        $user = User::findOrFail($attributes['user_id']);

        $yandexDisks = $user->clouds->where('type', Cloud::YandexDisk);
        foreach($yandexDisks as $disk) {
            if($disk->uid === $uid) {
                $disk->access_token = $access_token;
                $disk->save();
                return $disk;
            }
        }

        return Cloud::create(array_merge($attributes, ['uid' => $uid, 'type' => Cloud::YandexDisk]));
    }

    public function getContents($cloudId, $path)
    {
        $client = $this->getClient($cloudId);

        $contents = $client->directoryContents($path);

        /*if($contents[0]['resourceType'] === 'file') {
            $contents = [$this->shareStart($cloudId, $path)];
        }*/

        return $contents;
    }

    public function removeContent($cloudId, $path)
    {
        $client = $this->getClient($cloudId);

        return $client->delete($path) ? ['is_deleted' => true] : [];
    }

    public function moveContent($cloudId, $path, $newPath)
    {
        $client = $this->getClient($cloudId);

        return $client->move($path, $newPath) ? 'true' : 'false';
    }

    public function infoCloud($cloudId)
    {
        $client = $this->getClient($cloudId);

        $userInfo = $this->prepareData($client->getLogin());
        $diskInfo = $client->diskSpaceInfo();

        $diskInfo['login'] = $userInfo['login'];
        $diskInfo['name'] = $userInfo['fio'];

        return $diskInfo;
    }

    public function removeCloud($cloudId)
    {
        $cloud = $this->getCloud($cloudId);

        //We can't disable access token because we only delete from DB
        return $cloud->delete();
    }

    private function getClient($cloudId)
    {
        $cloud = $this->getCloud($cloudId);

        $client = new YandexDiskClient($cloud->access_token);

        return $client;
    }

    private function prepareData($data) {
        $response = [];
        $data = explode("\n", $data);

        foreach ($data as $item) {
            $list = explode(":", $item);
            if(array_key_exists(1, $list)) {
                $response[$list[0]] = $list[1];
            } else {
                array_push($response, $list[0]);
            }
        }

        return $response;
    }


    /**
     * @param int $cloudId
     * @param string $path
     * @return string Url
     */
    public function shareStart($cloudId, $path)
    {
        $client = $this->getClient($cloudId);

        return $client->startPublishing($path);
    }

    public function renameContent($cloudId, $path, $newPath)
    {
        $client = $this->getClient($cloudId);

        return $client->move($path, $newPath) ? 'true' : 'false';
    }

    public function copyContent($cloudId, $path, $newPath)
    {
        $client = $this->getClient($cloudId);

        return $client->copy($path, $newPath) ? 'true' : 'false';
    }

    public function downloadContents($cloudId, $cloudPath, $path)
    {
        $client = $this->getClient($cloudId);

        //Create dir
        $folder = storage_path() . '/app' . $path;
        mkdir($folder);

        \Log::debug('cloud path:');
        \Log::debug($cloudPath);
        $basePath = substr($cloudPath, 0, strrpos($cloudPath, "/"));
        $basePath = substr($basePath, 0, strrpos($basePath, "/"));
        \Log::debug('base cloud path:');
        \Log::debug($basePath);

        $contents = $client->directoryContents($cloudPath);

        if($contents[0]["resourceType"] === self::FILE) {
            $this->downloadFile($contents[0]["href"], $folder, $client, $basePath);
        }
        elseif($contents[0]["resourceType"] === self::FOLDER) {
            $this->downloadDir($cloudPath, $folder, $client, $basePath);
        }
    }

    private function downloadDir($cloudPath, $localPath, YandexDiskClient $client, $baseCloudPath)
    {
        $contents = $client->directoryContents($cloudPath);
        \Log::debug('subpath: ' . $this->getPathFromBase($contents[0]["href"], $baseCloudPath));
        $path = $localPath . $this->getPathFromBase($contents[0]["href"], $baseCloudPath);
        \Log::debug('next folder');
        \Log::debug($path);
        mkdir($path);
        array_shift($contents);
        foreach($contents as $content) {
            if($content["resourceType"] === self::FILE) {
                $path = $localPath . $content["href"];
                $this->downloadFile($content["href"], $path, $client, $baseCloudPath);
            }
            elseif($content["resourceType"] === self::FOLDER) {
                $this->downloadDir($content["href"], $localPath, $client, $baseCloudPath);
            }
        }
    }

    private function downloadFile($cloudPath, $localPath, YandexDiskClient $client, $baseCloudPath)
    {
        $path = $this->getPathFromBase($localPath, $baseCloudPath);
        $path = substr($path, 0, strrpos($path, "/") + 1);
        $client->downloadFile($cloudPath, $path);
    }

    public function uploadContents($cloudId, $cloudPath, $path)
    {
        //Get full path
        $path = $folder = storage_path() . '/app' . $path;

        //Get contents
        $contents = $this->getLocalContent($path);

        $client = $this->getClient($cloudId);

        foreach($contents as $content) {
            $contentPath = $path . '/' . $content;
            if(is_dir($contentPath)) {
                $tempCloudPath = $cloudPath . $content . '/';
                $client->createDirectory($tempCloudPath);
                $this->uploadDir($tempCloudPath, $contentPath, $client);
            }
            else {
                $this->uploadFile($cloudPath, $contentPath, $content, $client);
            }
        }
    }

    private function uploadDir($cloudPath, $localPath, YandexDiskClient $client)
    {
        //Get contents
        $contents = $this->getLocalContent($localPath);

        foreach($contents as $content) {
            $contentPath = $localPath . '/' . $content;
            if(is_dir($contentPath)) {
                $tempCloudPath = $cloudPath . $content . '/';
                $client->createDirectory($tempCloudPath);
                $this->uploadDir($tempCloudPath, $contentPath, $client);
            }
            else {
                $this->uploadFile($cloudPath, $contentPath, $content, $client);
            }
        }
    }

    private function uploadFile($cloudPath, $localPath, $newName, YandexDiskClient $client)
    {
        $client->uploadFile($cloudPath, [
            'path' => $localPath,
            'size' => filesize($localPath),
            'name' => $newName
        ]);
    }
}
