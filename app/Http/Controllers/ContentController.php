<?php namespace App\Http\Controllers;

use App\Cloud;
use App\Http\Requests;
use App\Services\ContentService;
use App\Services\DropBoxService;
use App\Services\YandexDiskService;
use App\Services\CloudActionService;
use Illuminate\Http\Request;

class ContentController extends Controller {

    protected $dropBoxService;

    protected $yandexDiskService;

    protected $contentService;

    protected $cloudService;

    public function __construct(DropBoxService $dropBoxService, YandexDiskService $yandexDiskService,
                                ContentService $contentService, CloudActionService $cloudService)
    {
        $this->dropBoxService = $dropBoxService;
        $this->yandexDiskService = $yandexDiskService;

        $this->contentService = $contentService;
        $this->cloudService = $cloudService;
    }

	/**
	 * Display a listing of the resource.
	 *
     * @param  int  $cloudId
	 * @return Response
	 */
	public function index($cloudId)
	{
        $contents = [];
        $cloud = Cloud::findOrFail((int)$cloudId);

        if($cloud->type === Cloud::DropBox) {
            $contents =  $this->dropBoxService->getContents($cloudId, '/');
        }
        elseif($cloud->type === Cloud::GoogleDrive) {
        }
        elseif($cloud->type === Cloud::YandexDisk) {
            $contents = $this->yandexDiskService->getContents($cloudId, '/');
        }

        $contents = $this->contentService->getContents($contents, $cloud);

        return $contents;
	}

	/**
	 * Show the form for creating a new resource.
     *
     * @param  int  $cloudId
	 * @return Response
	 */
	public function create($cloudId)
	{
        return [$cloudId];
	}

	/**
	 * Store a newly created resource in storage.
	 *
     * @param  int  $cloudId
	 * @return Response
	 */
	public function store($cloudId)
	{
        return [$cloudId];
	}

	/**
	 * Display the specified resource.
     *
     * @param  int  $cloudId
	 * @param  int  $path
     * @param  Request  $request
	 * @return Response
	 */
	public function show(Request $request, $cloudId, $path)
	{
        $cloud = Cloud::findOrFail((int)$cloudId);
        $path = $this->preparePath($path);
        if($request->exists('share')) {
            $response = [$this->cloudService->shareStart($cloud, $path)];
        } else {
            $contents = $this->cloudService->getContents($cloud, $path);

            $response = $this->contentService->getContents($contents, $cloud);
        }

        return $response;
	}

	/**
	 * Show the form for editing the specified resource.
	 *
     * @param  int  $cloudId
     * @param  int  $path
	 * @return Response
	 */
	public function edit($cloudId, $path)
	{
        return [$cloudId, $path];
	}

	/**
	 * Update the specified resource in storage.
	 *
     * @param  Request $request
     * @param  int  $cloudId
     * @param  int  $path
	 * @return Response
	 */
	public function update(Request $request, $cloudId, $path)
	{
        $cloud = Cloud::findOrFail((int)$cloudId);
        $path = $this->preparePath($path);

        if($cloud->type === Cloud::DropBox) {
            $response = $this->dropBoxService->moveContent($cloudId, $path, $request->get('newPath'));

        }
        elseif($cloud->type === Cloud::YandexDisk) {
            $response = $this->yandexDiskService->moveContent($cloudId, $path, $request->get('newPath'));
        }
        else {
            $response = [$cloudId, $path];
        }

        return $response;
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $path
     * @param  int  $cloudId
	 * @return Response
	 */
	public function destroy($cloudId, $path)
	{
        $cloud = Cloud::findOrFail((int)$cloudId);
        $path = $this->preparePath($path);

        if($cloud->type === Cloud::DropBox) {
            $response = $this->dropBoxService->removeContent($cloudId, $path);
        }
        elseif($cloud->type === Cloud::YandexDisk) {
            $response = $this->yandexDiskService->removeContent($cloudId, $path);
        }
        else {
            $response = 'I don\'t can remove files from not dropbox';
        }

        return $response;
	}


    private function preparePath($path)
    {
        return str_replace("\\", "/", $path);
    }
}
