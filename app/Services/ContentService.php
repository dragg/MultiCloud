<?php namespace App\Services;

use App\Cloud;
use App\Commands\MoveContent;
use App\Task;

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

    public function getContents($cloud, $path)
    {
        $contents = [];

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

        return $contents;
    }

    public function moveContent($cloudId, $path, $newPath)
    {
        $cloud = Cloud::findOrFail($cloudId);

        if($cloud->type === Cloud::DropBox) {
            $response = $this->dropBoxService->moveContent($cloudId, $path, $newPath);
        }
        elseif($cloud->type === Cloud::YandexDisk) {
            $response = $this->yandexDiskService->moveContent($cloudId, $path, $newPath);
        }
        elseif($cloud->type === Cloud::GoogleDrive) {
            $response = $this->googleDriveService->moveContent($cloudId, $path, $newPath);
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

        \Queue::push(
            new MoveContent($task, $this)
        );

        return $task;
    }

    public function renameContent($cloudId, $path, $newPath)
    {
        $response = [];

        $cloud = Cloud::findOrFail((int)$cloudId);

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

        return $response;
    }

    public function shareStart($cloud, $path)
    {
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

    private function getGooglePath($path)
    {
        $pos = strrpos($path, '/');
        return substr($path, ($pos !== FALSE ? $pos + 1 : 0));
    }

    public function copyContent($cloudId, $path, $newPath)
    {
        $cloud = Cloud::findOrFail($cloudId);

        if($cloud->type === Cloud::DropBox) {
            $response = $this->dropBoxService->copyContent($cloudId, $path, $newPath);
        }
        elseif($cloud->type === Cloud::YandexDisk) {
            $response = $this->yandexDiskService->copyContent($cloudId, $path, $newPath);
        }
        elseif($cloud->type === Cloud::GoogleDrive) {
            $response = $this->googleDriveService->copyContent($cloudId, $path, $newPath);
        }

        return $response;
    }
}