<?php namespace App\Services;

use App\Cloud;
use App\User;
use Illuminate\Support\Facades\Log;
use Yandex\Disk\DiskClient;

class YandexDiskService extends CloudService {

    public function create($attributes)
    {
        return $this->refreshOrCreate($attributes);
    }

    protected function refreshOrCreate($attributes)
    {
        $access_token = $attributes['access_token'];

        $diskClient = new DiskClient($access_token);
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

        return $contents;
    }

    public function removeContent($cloudId, $path)
    {
        $client = $this->getClient($cloudId);

        return $client->delete($path) ? ['is_deleted' => true] : [];
    }

    public function moveContent($cloudId, $path, $newPath)
    {
        // TODO: Implement moveContent() method.
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
        $cloud->delete();
    }

    private function getClient($cloudId)
    {
        $cloud = $this->getCloud($cloudId);

        $client = new \Yandex\Disk\DiskClient($cloud->access_token);

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
}
