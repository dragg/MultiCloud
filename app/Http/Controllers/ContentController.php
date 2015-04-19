<?php namespace App\Http\Controllers;

use App\Cloud;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Services\DropBoxService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ContentController extends Controller {

    protected $dropBoxService;

    public function __construct(DropBoxService $dropBoxService)
    {
        $this->dropBoxService = $dropBoxService;
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
        $path = $this->preparePath($path);
        return $this->dropBoxService->getContents($cloudId, $path);
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
