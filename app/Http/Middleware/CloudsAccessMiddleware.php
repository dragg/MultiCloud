<?php namespace App\Http\Middleware;

use App\Cloud;
use Closure;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CloudAccessMiddleware {

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
        // get path as 'clouds/{id}/contents/{contentPath}'
        $decodedPath = $request->decodedPath();

        // get path as '{id}/contents/{contentPath}'
        $cloudId = substr($decodedPath, strpos($decodedPath, '/') + 1);

        // get a cloud id
        // check because we may had a path as 'clouds/{id}'
        $findNextIndex = strpos($cloudId, '/');
        if($findNextIndex) {
            $cloudId = substr($cloudId, 0, $findNextIndex);
        }

        try {
            $cloud = Cloud::findOrFail($cloudId);
            if($cloud->user_id !== \Auth::user()->id) {
                throw new \Exception('It\'s not your cloud. Permission denied!');
            }
        }
        catch(ModelNotFoundException $ex) {
            return response('Resource not found', 404);
        }
        catch(\Exception $ex) {
            return response($ex->getMessage(), 403);
        }

		return $next($request);
	}

}
