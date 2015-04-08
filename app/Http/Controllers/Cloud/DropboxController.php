<?php namespace App\Http\Controllers\Cloud;

use App\DropBox;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Dropbox as dbx;
use Illuminate\Support\Facades\Log;

class DropboxController extends Controller {

    private static $clientIdentifier = "MultiCloudThesis alpha";

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
        $dropbox = DropBox::findOrFail((int)$id);
        if(Auth::user()->id === $dropbox->user_id)
        {
            $client = new dbx\Client($dropbox->access_token, self::$clientIdentifier);
            $response = $client->getAccountInfo();
        }
        else {
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
        $dropbox = DropBox::findOrFail((int)$id);
        if(Auth::user()->id === $dropbox->user_id)
        {
            $client = new dbx\Client($dropbox->access_token, self::$clientIdentifier);
            $client->disableAccessToken();
            $dropbox->delete();
            return redirect('/home');
        }
        else {
            $response = "Access denied!";
            return $response;
        }
	}

}
