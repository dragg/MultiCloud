<?php namespace App\Services;

use App\Cloud;

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

    public function rename($id, $name)
    {
        $cloud = $this->getCloud($id);

        if($cloud->type === Cloud::DropBox) {
            $response = $this->dropBoxService->renameCloud($id, $name);
        }
        elseif ($cloud->type === Cloud::GoogleDrive) {
            $response = $this->googleDriveService->renameCloud($id, $name);
        }
        elseif ($cloud->type === Cloud::YandexDisk) {
            $response = $this->yandexDiskService->renameCloud($id, $name);
        }
        else {
            $response = ["Error of type cloud"];
        }

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
            $response = ["Error of type cloud"];
        }

        return $response;
    }

    private function getCloud($id)
    {
        return Cloud::findOrFail((int)$id);
    }

}