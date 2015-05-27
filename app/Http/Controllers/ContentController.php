<?php namespace App\Http\Controllers;

use App\Cloud;
use App\Http\Requests;
use App\Services\FormatContentService;
use App\Services\DropBoxService;
use App\Services\YandexDiskService;
use App\Services\ContentService;
use Illuminate\Http\Request;

class ContentController extends Controller {

    protected $dropBoxService;

    protected $yandexDiskService;

    protected $formatService;

    protected $contentService;

    public function __construct(DropBoxService $dropBoxService, YandexDiskService $yandexDiskService,
                                FormatContentService $formatService, ContentService $contentService)
    {
        $this->dropBoxService = $dropBoxService;
        $this->yandexDiskService = $yandexDiskService;

        $this->formatService = $formatService;
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

        $contents = $this->getContents($cloud, '/');

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
            $response = [$this->contentService->shareStart($cloud, $path)];
        } else {
            $response = $this->getContents($cloud, $path);
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
        $path = $this->preparePath($path);

        if($request->exists('newCloudId') && $request->exists('newPath')) {
            $response = $this->contentService
                ->taskToMove($cloudId, $path, $request->get('newCloudId'), $request->get('newPath'));
        }
        elseif($request->exists('newPath')) {
            $response = $this->contentService->renameContent($cloudId, $path, $request->get('newPath'));
        }
        else {
            //throw exception or smt
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

    /**
     * @param $cloud
     * @param $path
     * @return array
     */
    private function getContents($cloud, $path)
    {
        $contents = $this->contentService->getContents($cloud, $path);

        $response = $this->formatService->getContents($contents, $cloud);

        return $response;
    }
}
