<?php namespace App\Http\Controllers\Cloud;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\YandexDisk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Yandex\Disk\DiskClient;

class YandexController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{

	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
        $yDisk = YandexDisk::findOrFail((int)$id);
        if($yDisk->user_id === Auth::user()->id) {
            $diskClient = new DiskClient(YandexDisk::findOrFail((int)$id)->access_token);
            $diskClient->setServiceScheme(DiskClient::HTTPS_SCHEME);

            $dirContent = $diskClient->directoryContents('/');

            $contents = [];
            $i = 0;
            foreach ($dirContent as $dirItem) {
                if ($dirItem['resourceType'] === 'dir') {
                    $contents[$i++] = 'Directory "' . $dirItem['displayName'] . '" was create ' . date(
                            'Y-m-d by H:i:s',
                            strtotime($dirItem['creationDate'])
                        ) . '<br />';
                } else {
                    $contents[$i++] = 'File "' . $dirItem['displayName'] . '" with size ' . $dirItem['contentLength'] . ' was create ' . date(
                            'Y-m-d by H:i:s',
                            strtotime($dirItem['creationDate'])
                        ) . '<br />';
                }
            }

            $response = $contents;
        } else {
            $response = "Access denied!";
        }

        return $response;
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
        $yDisk = YandexDisk::findOrFail((int)$id);
        if($yDisk->user_id === Auth::user()->id) {
            $yDisk->delete();
            return redirect('/home');
        } else {
            return $response = "Access denied!";
        }
	}

}
