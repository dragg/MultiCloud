<?php namespace App\Services;

use App\Cloud;
use App\User;
use Yandex\Disk\DiskClient;

class YandexDiskServices {

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

}
