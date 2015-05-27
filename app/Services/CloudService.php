<?php namespace App\Services;

use App\Cloud;

abstract class CloudService {

    abstract public function getContents($cloudId, $path);
    abstract public function removeContent($cloudId, $path);
    abstract public function copyContent($cloudId, $path, $newPath);
    abstract public function moveContent($cloudId, $path, $newPath);
    abstract public function renameContent($cloudId, $fileId, $newTitle);
    abstract public function shareStart($cloudId, $path);

    abstract public function create($attributes);
    abstract public function infoCloud($cloudId);
    abstract public function removeCloud($cloudId);

    public function renameCloud($cloudId, $name)
    {
        $cloud = $this->getCloud($cloudId);

        $cloud->name = $name;
        $cloud->save();

        return $cloud;
    }

    protected function getCloud($cloudId)
    {
        //need catch exception
        $cloud = Cloud::findOrFail((int)$cloudId);
        return $cloud;
    }

}