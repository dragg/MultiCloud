<?php namespace App\Http\Controllers;

use \Response;
use App\Http\Requests;
use App\Services\CloudActionService;
use Illuminate\Http\Request;
use \Auth;

class CloudController extends Controller {

    protected static $rules = [
        'user_id' => 'required|exists:users,id',
        'access_token' => 'required|string',
        'uid' => 'required|string',
        'name' => 'required|unique|string',
        'type' => 'required|integer|min:1|max:3',
        'token_type' => 'string',
        'expires_in' => 'string',
        'created' => 'string'
    ];

    protected $cloudActionService;

    public function __construct(CloudActionService $cloudActionService)
    {
        $this->cloudActionService = $cloudActionService;
        $this->middleware('clouds.access', ['except' => 'index', 'store']);
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

    public function store(Request $request)
    {
        $this->validate($request, self::$rules);

        return $this->cloudActionService->create($request->all());
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
        return $this->cloudActionService->update($id, $request->all());
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
        return response()->json($this->cloudActionService->remove($id));
	}
}
