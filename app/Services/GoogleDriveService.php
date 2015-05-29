<?php namespace App\Services;

use App\Cloud;
use App\User;
use \Config;
use Google_Service_Oauth2;
use Google_Client;

class GoogleDriveService extends CloudService {

    const
        GOOGLE_FOLDER = 'application/vnd.google-apps.folder',
        GOOGLE_FILE = 'application/vnd.google-apps.file',
        GOOGLE_UPLOAD = 'application/octet-stream';

    public function create($attributes)
    {
        return $this->refreshOrCreate($attributes);
    }

    protected function refreshOrCreate($attributes)
    {
        $access_token = $attributes['access_token'];

        $client = $this->getClient($attributes);
        $authService = new Google_Service_Oauth2($client);
        $uid = $authService->userinfo->get()->id;
        $user = User::findOrFail($attributes['user_id']);

        $googleDrives = $user->clouds->where('type', Cloud::GoogleDrive);
        foreach($googleDrives as $drive) {
            if($drive->uid === $uid) {
                $drive->access_token = $access_token;
                $drive->save();
                return $drive;
            }
        }

        return Cloud::create(array_merge($attributes, ['uid' => $uid, 'type' => Cloud::GoogleDrive]));
    }


    public function getClient($attributes)
    {
        $client = new Google_Client();
        $client->setClientSecret(Config::get('clouds.google_drive.secret'));
        $client->setClientId(Config::get('clouds.google_drive.id'));

        if($attributes instanceof Cloud) {
            $client->setAccessToken(json_encode([
                'access_token' => $attributes->access_token,
                'token_type' => $attributes->token_type,
                'expires_in' => $attributes->expires_in,
                'created' => $attributes->created
            ]));
        } else {
            $client->setAccessToken(json_encode([
                'access_token' => $attributes['access_token'],
                'token_type' => $attributes['token_type'],
                'expires_in' => $attributes['expires_in'],
                'created' => $attributes['created']
            ]));
        }

        return $client;
    }

    /**
     * @param $cloudId
     * @param $path
     * @return \Google_Service_Drive_DriveFile[]
     */
    public function getContents($cloudId, $path)
    {
        $service = $this->getService($cloudId);

        $response = [];

        $files = $service->children->listChildren($path)->getItems();

        foreach($files as $file) {
            $_file = $service->files->get($file->getId());
            array_push($response, $_file);
        }
        return $response;
    }

    public function removeContent($cloudId, $path)
    {
        $service = $this->getService($cloudId);
        $response = $service->files->delete($path);

        return $response;
    }

    public function moveContent($cloudId, $path, $newPath)
    {
        try {
            $this->copyContent($cloudId, $path, $newPath);

            return $this->removeContent($cloudId, $path);
        } catch(\Exception $ex) {
            return $ex->getMessage();
        }
    }

    public function infoCloud($cloudId)
    {
        //ToDo: do it!
        //$client = $this->getClient($cloudId);
    }

    public function removeCloud($cloudId)
    {
        $cloud = $this->getCloud($cloudId);
        return $cloud->delete();
    }

    public function shareStart($cloudId, $path)
    {
        $service = $this->getService($cloudId);

        $permission = new \Google_Service_Drive_Permission();
        $permission->setValue('');
        $permission->setType('anyone');
        $permission->setRole('reader');

        $service->permissions->insert($path, $permission);

        //return file
        return $service->files->get($path);
    }

    public function renameContent($cloudId, $fileId, $newTitle)
    {
        $service = $this->getService($cloudId);
        $file = $service->files->get($fileId);

        $file->setTitle($newTitle);

        try {
            $response = $service->files->update($fileId, $file) ? 'true' : 'false';
        }
        catch (\Google_Service_Exception $ex) {
            $response = $ex->getMessage();
        }

        return $response;
    }

    public function getService($cloudId)
    {
        $googleDrive = $this->getCloud($cloudId);
        $client = $this->getClient($googleDrive);
        return new \Google_Service_Drive($client);
    }

    public function copyContent($cloudId, $path, $newPath)
    {
        $service = $this->getService($cloudId);

        $copiedFile = new \Google_Service_Drive_DriveFile();
        $copiedFile->setTitle($newPath);

        $response = $service->files->copy($path, $copiedFile);

        return $response;
    }

    public function downloadContents($cloudId, $cloudPath, $path)
    {
        //Get file
        $service = $this->getService($cloudId);
        $file = $service->files->get($cloudPath);

        //Create dir
        $folderPath = storage_path() . '/app' . $path;
        mkdir($folderPath);

        //Download
        if($file->getMimeType() === self::GOOGLE_FOLDER) {
            $this->downloadDir($cloudId, $cloudPath, $file->getTitle(), $folderPath);
        }
        else {
            $this->downloadFile($cloudId, $cloudPath, $folderPath);
        }
    }

    private function downloadDir($cloudId, $cloudPath, $folderTitle, $folderPath)
    {
        //Create nest folder
        $folderPath = $folderPath . '/' . $folderTitle;
        mkdir($folderPath);

        $contents = $this->getContents($cloudId, $cloudPath);
        foreach($contents as $content) {
            if($content->getMimeType()  === self::GOOGLE_FOLDER) {
                $this->downloadDir($cloudId, $content->getId(), $content->getTitle(), $folderPath);
            }
            else {
                $this->downloadFile($cloudId, $content->getId(), $folderPath);
            }
        }
    }

    private function downloadFile($cloudId, $fileId, $path)
    {
        //Get file
        $service = $this->getService($cloudId);
        $file = $service->files->get($fileId);

        //Get info about file
        $fileName = $file->getTitle();
        $pathFile = $path . '/' . $fileName;

        //Get link to file
        $fileContent = $this->googleDownloadFile($service, $fileId);

        //Download File
        file_put_contents($pathFile, $fileContent);
    }

    private function googleDownloadFile(\Google_Service_Drive $service, $fileId)
    {
        $file = $service->files->get($fileId);

        $downloadUrl = $file->getDownloadUrl();

        if ($downloadUrl) {
            $request = new \Google_Http_Request($downloadUrl, 'GET', null, null);
            $httpRequest = $service->getClient()->getAuth()->authenticatedRequest($request);
            if ($httpRequest->getResponseHttpCode() == 200) {
                return $httpRequest->getResponseBody();
            } else {
                // An error occurred.
                return null;
            }
        } else {
            // The file doesn't have any content stored on Drive.
            return null;
        }
    }

    public function uploadContents($cloudId, $cloudPath, $path)
    {
        $service = $this->getService($cloudId);

        //Get full path
        $path = $folder = storage_path() . '/app' . $path;

        //Get contents
        $contents = $this->getLocalContent($path);

        foreach($contents as $content) {
            $contentPath = $path . '/' . $content;
            if(is_dir($contentPath)) {
                $newFolder = $this->insertFile($service, $content, $cloudPath, self::GOOGLE_FOLDER, $contentPath);
                $this->uploadDir($newFolder->getId(), $contentPath, $service);
            }
            else {
                $this->insertFile($service, $content, $cloudPath, self::GOOGLE_FILE, $contentPath);
            }
        }
    }

    private function uploadDir($folderId, $filePath, $service)
    {
        //Get contents
        $contents = $this->getLocalContent($filePath);

        foreach($contents as $content) {
            $contentPath = $filePath . '/' . $content;
            if(is_dir($contentPath)) {
                $newFolder = $this->insertFile($service, $content, $folderId, self::GOOGLE_FOLDER, $contentPath);
                $this->uploadDir($newFolder->getId(), $contentPath, $service);
            }
            else {
                $this->insertFile($service, $content, $folderId, self::GOOGLE_UPLOAD, $contentPath);
            }
        }
    }

    private function insertFile(\Google_Service_Drive $service, $title, $parentId, $mimeType, $filePath)
    {
        $file = new \Google_Service_Drive_DriveFile();
        $file->setTitle($title);
        $file->setMimeType($mimeType);

        // Set the parent folder.
        if ($parentId != null) {
            $parent = new \Google_Service_Drive_ParentReference();
            $parent->setId($parentId);
            $file->setParents(array($parent));
        }

        try {
            $data = file_get_contents($filePath);

            $createdFile = $service->files->insert($file, array(
                'data' => $data,
                "uploadType" => $mimeType === self::GOOGLE_FOLDER ? "resumable" : "media",
                'mimeType' => $mimeType,
            ));

            return $createdFile;
        } catch (\Exception $e) {
            \Log::debug("An error occurred: " . $e->getMessage());
            return null;
        }
    }

}
