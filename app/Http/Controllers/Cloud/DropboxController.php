<?php namespace App\Http\Controllers\Cloud;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Dropbox as dbx;

class DropboxController extends Controller {

    private static $clientIdentifier = "MultiCloudThesis alpha";

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
    {
        $client = new dbx\Client(Auth::user()->accessTokenDropbox, self::$clientIdentifier);
        return $client->getAccountInfo();
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
		//
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
	public function destroy()
	{
        $client = new dbx\Client(Auth::user()->accessTokenDropbox, self::$clientIdentifier);
        Auth::user()->accessTokenDropbox = null;
        Auth::user()->save();
        $client->disableAccessToken();
        return redirect('/home');
	}

}
