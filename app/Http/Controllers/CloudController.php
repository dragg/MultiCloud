<?php namespace App\Http\Controllers;

use \Response;
use App\Cloud;
use App\Http\Requests;
use App\Services\CloudActionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CloudController extends Controller {

    protected $cloudActionService;

    public function __construct(CloudActionService $cloudActionService)
    {
        $this->cloudActionService = $cloudActionService;
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
        return $this->cloudActionService->getInfo($id);
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
        return $this->cloudActionService->rename($id, $request->get('name'));
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
        $this->cloudActionService->remove($id);
        return $response;
	}



}
