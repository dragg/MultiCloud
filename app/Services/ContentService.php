<?php namespace App\Services;

use App\Cloud;

class ContentService {

    public function getContents($contents, $cloudType)
    {
        $response = [];

        if($cloudType === Cloud::DropBox) {
            foreach($contents as $content) {
                //Create common interface
                $temp = [
                    'is_dir' => $content['is_dir'],
                    'path' => $content['name'],
                    'size' => $content['size'],
                    'updated_at' => $content['modified']
                ];

                //Add to response
                $response[] = $temp;
            }
        }
        elseif($cloudType === Cloud::YandexDisk) {

            if(!is_array($contents)) {
                return [$contents];
            }

            array_shift($contents);
            foreach($contents as $content) {
                //Create common interface
                $temp = [
                    'is_dir' => ($content['resourceType'] === 'dir'),
                    'path' => $content['href'],
                    'size' => $content['contentLength'],
                    'updated_at' => $content['lastModified'],
                    'display_name' => $content['displayName']
                ];

                //Add to response
                $response[] = $temp;
            }
        }

        return $response;
    }
}