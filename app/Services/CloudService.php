<?php namespace App\Services;


abstract class CloudService {

    abstract public function getContents($cloudId, $path);
    abstract public function removeContent($cloudId, $path);
    abstract public function moveContent($cloudId, $path, $newPath);

    abstract public function create($attributes);
    abstract public function infoCloud($cloudId);
    abstract public function removeCloud($cloudId);
    abstract public function renameCloud($cloudId, $name);


}