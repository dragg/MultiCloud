<?php namespace App\Services;

use App\Cloud;
use Illuminate\Support\Facades\Log;

class CloudActionService {

    protected $dropBoxService;

    protected $yandexDiskService;

    protected $contentService;

    public function __construct(DropBoxService $dropBoxService, YandexDiskService $yandexDiskService,
                                ContentService $contentService)
    {
        $this->dropBoxService = $dropBoxService;
        $this->yandexDiskService = $yandexDiskService;

        $this->contentService = $contentService;
    }

    public function getContents($cloud, $path)
    {
        $contents = [];

        if($cloud->type === Cloud::DropBox) {
            $contents = $this->dropBoxService->getContents($cloud->id, $path);
        }
        elseif($cloud->type === Cloud::GoogleDrive) {
        }
        elseif($cloud->type === Cloud::YandexDisk) {
            $contents = $this->yandexDiskService->getContents($cloud->id, $path);
        }

        return $contents;
    }

}