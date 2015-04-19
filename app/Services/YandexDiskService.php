<?php namespace App\Services;

use App\Cloud;
use App\User;
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
        // TODO: Implement getContents() method.
    }

    public function removeContent($cloudId, $path)
    {
        // TODO: Implement removeContent() method.
    }

    public function moveContent($cloudId, $path, $newPath)
    {
        // TODO: Implement moveContent() method.
    }

    public function infoCloud($cloudId)
    {
        // TODO: Implement infoCloud() method.
    }

    public function removeCloud($cloudId)
    {
        // TODO: Implement removeCloud() method.
    }

    public function renameCloud($cloudId, $name)
    {
        // TODO: Implement renameCloud() method.
    }
}
