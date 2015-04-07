<?php namespace App\Http\Controllers\Cloud;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Google_Service_Drive;
use Google_Auth_OAuth2;
use Google_Client;

class GoogleController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$client = new Google_Client();
        $client->setClientSecret(\Config::get('clouds.google_drive.secret'));
        $client->setClientId(\Config::get('clouds.google_drive.id'));

        $client->setAccessToken(json_encode([
            'access_token' => \Auth::user()->accessTokenGoogle,
            'token_type' => \Auth::user()->token_type_google,
            'expires_in' => \Auth::user()->expires_in_google,
            'created' => \Auth::user()->created_google
        ]));

        $service = new Google_Service_Drive($client);

        return ($service->files->listFiles()->getItems());
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
        \Auth::user()->accessTokenGoogle = null;
        \Auth::user()->save();
        return redirect('/home');
	}

}
