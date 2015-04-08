<?php namespace App\Http\Controllers\Cloud;

use App\GoogleDrive;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Google_Service_Drive;
use Google_Auth_OAuth2;
use Google_Client;
use App\Services\GoogleDriveService;

class GoogleController extends Controller {

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
        $gDrive = GoogleDrive::findOrFail((int)$id);
        if(\Auth::user()->id === $gDrive->user_id)
        {
            $gService = new GoogleDriveService();
            $auth = new \Google_Service_Oauth2($gService->getClient($gDrive));
            return (array)$auth->userinfo->get()->toSimpleObject();
        }
        else {
            $response = "Access denied!";
            return $response;
        }
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
        $gDrive = GoogleDrive::findOrFail((int)$id);
        if(\Auth::user()->id === $gDrive->user_id)
        {
            $gDrive->delete();
        }
        else {
            $response = "Access denied!";
            return $response;
        }
        return redirect('/home');
	}

}
