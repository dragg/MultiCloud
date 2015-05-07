<?php namespace App\Http\Controllers;

use App\Cloud;
use App\Http\Requests;
use App\Services\ContentService;
use App\Services\DropBoxService;
use App\Services\YandexDiskService;
use Illuminate\Http\Request;

class ContentController extends Controller {

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

	/**
	 * Display a listing of the resource.
	 *
     * @param  int  $cloudId
	 * @return Response
	 */
	public function index($cloudId)
	{
        $cloud = Cloud::findOrFail((int)$cloudId);
        if($cloud->type === Cloud::DropBox) {
            return $this->dropBoxService->getContents($cloudId, '/');
        }
        return [$cloudId];
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
	 * @return Response
	 */
	public function show($cloudId, $path)
	{
        $cloud = Cloud::findOrFail((int)$cloudId);
        $path = $this->preparePath($path);
        $contents = [];

        if($cloud->type === Cloud::DropBox) {
            $contents = $this->dropBoxService->getContents($cloudId, $path);
        }
        elseif($cloud->type === Cloud::GoogleDrive) {
        }
        elseif($cloud->type === Cloud::YandexDisk) {
            $contents = $this->yandexDiskService->getContents($cloudId, $path);
        }

        $contents = $this->contentService->getContents($contents, $cloud->type);

        return $contents;
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
        if($cloud->type === Cloud::DropBox) {
            $path = $this->preparePath($path);
            $response = $this->dropBoxService->moveContent($cloudId, $path, $request->get('newPath'));
            return $response;
        }

        return [$cloudId, $path];
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
        if($cloud->type === Cloud::DropBox) {
            $path = $this->preparePath($path);
            $response = $this->dropBoxService->removeContent($cloudId, $path);
            return $response;
        }
		return 'I don\'t can remove files from not dropbox';
	}


    private function preparePath($path)
    {
        return str_replace("\\", "/", $path);
    }
}
