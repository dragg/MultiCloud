<?php namespace App\Http\Controllers;

use App\Cloud;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ContentController extends Controller {

    private static $clientIdentifier = "MultiCloudThesis alpha";

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
            $client = new \Dropbox\Client($cloud->access_token, self::$clientIdentifier);

            $metadata = $client->getMetadataWithChildren("/");
            $contents = [];
            foreach($metadata["contents"] as $content) {
                array_push($contents, [$content['path'], $content['is_dir']]);
            }
            return $contents;

        }
        return [$cloudId];
	}

	/**
	 * Show the form for creating a new resource.
	 *
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
     * @param  int  $cloudId
	 * @param  int  $id
	 * @return Response
	 */
	public function show($cloudId, $id)
	{
        return [$cloudId, $id];
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($cloudId, $id)
	{
        return [$cloudId, $id];
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($cloudId, $id)
	{
        return [$cloudId, $id];
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($cloudId, $id)
	{
		return [$cloudId, $id];
	}

}
