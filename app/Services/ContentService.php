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

        return $response;
    }
}