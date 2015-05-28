<?php namespace App\Services;

use App\Cloud;
use App\Commands\MoveContent;
use App\Task;
use \Queue;

class ContentService {

    protected $dropBoxService;

    protected $yandexDiskService;

    protected $googleDriveService;

    public function __construct(DropBoxService $dropBoxService, YandexDiskService $yandexDiskService,
                                GoogleDriveService $googleDriveService)
    {
        $this->dropBoxService = $dropBoxService;
        $this->yandexDiskService = $yandexDiskService;
        $this->googleDriveService = $googleDriveService;
    }

    public function getContents($cloudId, $path)
    {
        $cloud = $this->getCloud($cloudId);
        if($cloud->type === Cloud::DropBox) {
            $contents = $this->dropBoxService->getContents($cloud->id, $path);
        }
        elseif($cloud->type === Cloud::GoogleDrive) {
            $path = ($path === '/') ? 'root' : $path;
            $path = $this->getGooglePath($path);
            $contents = $this->googleDriveService->getContents($cloud->id, $path);
        }
        elseif($cloud->type === Cloud::YandexDisk) {
            $contents = $this->yandexDiskService->getContents($cloud->id, $path);
        }
        else {
            $contents = "Error of type cloud";
        }

        return $contents;
    }

    public function moveContent($cloudId, $path, $newPath)
    {
        $cloud = $this->getCloud($cloudId);

        if($cloud->type === Cloud::DropBox) {
            $response = $this->dropBoxService->moveContent($cloudId, $path, $newPath);
        }
        elseif($cloud->type === Cloud::YandexDisk) {
            $response = $this->yandexDiskService->moveContent($cloudId, $path, $newPath);
        }
        elseif($cloud->type === Cloud::GoogleDrive) {
            $response = $this->googleDriveService->moveContent($cloudId, $path, $newPath);
        }
        else {
            $response = "Error of type cloud";
        }

        return $response;
    }

    public function taskToMove($cloudId, $path, $moveCloudId, $newPath)
    {
        $task = Task::create([
            'cloudIdFrom' => $cloudId,
            'pathFrom' => $path,
            'cloudIdTo' => $moveCloudId,
            'pathTo' => $newPath
        ]);

        Queue::push(
            new MoveContent($task, $this)
        );

        return $task;
    }

    public function renameContent($cloudId, $path, $newPath)
    {
        $cloud = $this->getCloud($cloudId);

        if($cloud->type === Cloud::DropBox) {
            $response = $this->dropBoxService->renameContent($cloudId, $path, $newPath);
        }
        elseif($cloud->type === Cloud::YandexDisk) {
            $response = $this->yandexDiskService->renameContent($cloudId, $path, $newPath);
        }
        elseif($cloud->type === Cloud::GoogleDrive) {
            $response = $this->googleDriveService->renameContent($cloudId,
                $this->getGooglePath($path), $this->getGooglePath($newPath));
        }
        else {
            $response = "Error of type cloud";
        }

        return $response;
    }

    public function shareStart($cloudId, $path)
    {
        $cloud = $this->getCloud($cloudId);
        $url = '';

        if($cloud->type === Cloud::DropBox) {
            $url = $this->dropBoxService->shareStart($cloud->id, $path);
        }
        elseif($cloud->type === Cloud::GoogleDrive) {
            $url = $this->googleDriveService->shareStart($cloud->id, $path);
        }
        elseif($cloud->type === Cloud::YandexDisk) {
            $url = $this->yandexDiskService->shareStart($cloud->id, $path);
        }

        return $url;
    }

    public function copyContent($cloudId, $path, $newPath)
    {
        $cloud = $this->getCloud($cloudId);

        if($cloud->type === Cloud::DropBox) {
            $response = $this->dropBoxService->copyContent($cloudId, $path, $newPath);
        }
        elseif($cloud->type === Cloud::YandexDisk) {
            $response = $this->yandexDiskService->copyContent($cloudId, $path, $newPath);
        }
        elseif($cloud->type === Cloud::GoogleDrive) {
            $response = $this->googleDriveService->copyContent($cloudId, $path, $newPath);
        }
        else {
            $response = "Error of type cloud";
        }

        return $response;
    }

    public function removeContent($cloudId, $path)
    {
        $cloud = $this->getCloud($cloudId);
        $path = $this->preparePath($path);

        if($cloud->type === Cloud::DropBox) {
            $response = $this->dropBoxService->removeContent($cloudId, $path);
        }
        elseif($cloud->type === Cloud::YandexDisk) {
            $response = $this->yandexDiskService->removeContent($cloudId, $path);
        }
        elseif($cloud->type === Cloud::GoogleDrive) {
            $response = $this->googleDriveService->removeContent($cloudId, $path);
        }
        else {
            $response = "Error of type cloud";
        }

        return $response;
    }

    private function getGooglePath($path)
    {
        $pos = strrpos($path, '/');
        return substr($path, ($pos !== FALSE ? $pos + 1 : 0));
    }

    private function preparePath($path)
    {
        return str_replace("\\", "/", $path);
    }

    public function getCloud($cloudId)
    {
        return Cloud::findOrFail((int)$cloudId);
    }
}