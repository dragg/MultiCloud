<?php namespace App\Services;

use App\Cloud;
use \Auth;

class CloudActionService {

    protected $dropBoxService;

    protected $googleDriveService;

    protected $yandexDiskService;

    public function __construct(DropBoxService $dropBoxServices,
                                GoogleDriveService $googleDriveService,
                                YandexDiskService $yandexDiskService)
    {
        $this->dropBoxService = $dropBoxServices;
        $this->googleDriveService = $googleDriveService;
        $this->yandexDiskService = $yandexDiskService;
    }

    public function create($attributes)
    {
        return Cloud::create(array_merge($attributes, ['user_id' => Auth::user()->id]));
    }

    public function getInfo($id)
    {
        $cloud = $this->getCloud($id);

        if($cloud->type === Cloud::DropBox) {
            $response = $this->dropBoxService->infoCloud($id);
        }
        elseif ($cloud->type === Cloud::GoogleDrive) {
            $response = $this->googleDriveService->infoCloud($id);
        }
        elseif ($cloud->type === Cloud::YandexDisk) {
            $response = $this->yandexDiskService->infoCloud($id);
        }
        else {
            $response = ["Error of type cloud"];
        }

        $response["cloud"] = $cloud;

        return $response;
    }

    public function remove($id)
    {
        $cloud = $this->getCloud($id);

        if($cloud->type === Cloud::DropBox) {
            $response = $this->dropBoxService->removeCloud($id);
        }
        elseif ($cloud->type === Cloud::GoogleDrive) {
            $response = $this->googleDriveService->removeCloud($id);
        }
        elseif ($cloud->type === Cloud::YandexDisk) {
            $response = $this->yandexDiskService->removeCloud($id);
        }
        else {
            $response = "Error of type cloud";
        }

        return $response;
    }

    public function update($id, $attributes)
    {
        $cloud = $this->getCloud($id);

        $properties = ['name', 'uid', 'type','access_token', 'token_type', 'expires_in', 'created'];

        foreach($properties as $property) {
            if(array_key_exists($property, $attributes)) {
                $cloud->$property = $attributes[$property];
            }
        }

        $cloud->save();

        return $cloud;
    }

    /**
     * @param $id Integer It's cloud's id
     * @return \App\Cloud
     */

    private function getCloud($id)
    {
        return CloudService::getCloud($id);
    }

}