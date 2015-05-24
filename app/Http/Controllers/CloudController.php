<?php namespace App\Http\Controllers;

use App\Cloud;
use App\Http\Requests;
use App\Services\DropBoxService;
use App\Services\GoogleDriveService;
use App\Services\YandexDiskService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CloudController extends Controller {

    protected $dropBoxService;

    protected $googleDriveService;

    protected $yandexDiskService;

    public function __construct(DropBoxService $dropBoxServices,
                                GoogleDriveService $googleDriveService,
                                YandexDiskService $yandexDiskService)
    {
        $this->dropBoxService = $dropBoxServices;
        $this->googleDriveService = $googleDriveService;
        $this->yandexDiskService = $yandexDiskService;
    }

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
        return Auth::user()->clouds;
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
        $response = [];
        $cloud = $this->getCloud($id);
        if($cloud->type === Cloud::DropBox) {
            $response = $this->dropBoxService->infoCloud($id);
        }
        elseif ($cloud->type === Cloud::GoogleDrive) {
            $response = $this->googleDriveService->infoCloud($id);
        }
        elseif ($cloud->type === Cloud::YandexDisk) {
            $response = $this->yandexDiskService->infoCloud($id);
        }
        $response["cloud"] = $cloud;
        return $response;
	}

	/**
	 * Update the specified resource in storage.
	 *
     * @param   Request $request
	 * @param   int $id
	 * @return  Response
	 */
	public function update(Request $request, $id)
	{
        $response = [];
        $cloud = $this->getCloud($id);
        $name = $request->get('name');
        if($cloud->type === Cloud::DropBox) {
            $response = $this->dropBoxService->renameCloud($id, $name);
        }
        elseif ($cloud->type === Cloud::GoogleDrive) {
            $response = $this->googleDriveService->renameCloud($id, $name);
        }
        elseif ($cloud->type === Cloud::YandexDisk) {
            $response = $this->yandexDiskService->renameCloud($id, $name);
        }
        return $response;
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
        $response = 'ok';
		$cloud = $this->getCloud($id);
        if($cloud->type === Cloud::DropBox) {
            $this->dropBoxService->removeCloud($id);
        }
        elseif ($cloud->type === Cloud::GoogleDrive) {
            $this->googleDriveService->removeCloud($id);
        }
        elseif ($cloud->type === Cloud::YandexDisk) {
            $this->yandexDiskService->removeCloud($id);
        }
        return $response;
	}

    private function getCloud($id)
    {
        //need catch exception
        $cloud = Cloud::findOrFail((int)$id);

        if(Auth::user()->id !== $cloud->user->id) {
            //through exception
        }

        return $cloud;
    }

}
