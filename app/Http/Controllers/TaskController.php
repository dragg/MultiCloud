<?php namespace App\Http\Controllers;

use App\Http\Requests;
use \Response;
use \Auth;

class TaskController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		return Auth::user()->tasks;
	}

}
