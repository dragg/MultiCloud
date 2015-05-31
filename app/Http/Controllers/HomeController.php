<?php namespace App\Http\Controllers;

use \Response;

class HomeController extends Controller {

	/**
	 * Show the application dashboard to the user.
	 *
	 * @return Response
	 */
	public function index()
	{
        return view('layout');
	}

}
