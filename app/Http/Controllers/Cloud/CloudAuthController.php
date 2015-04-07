<?php namespace App\Http\Controllers\Cloud;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Dropbox as dbx;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Yandex\OAuth\OAuthClient;
use Yandex\OAuth\Exception\AuthRequestException;

class CloudAuthController extends Controller {

    private static $clientIdentifier = "MultiCloudThesis alpha";

	public function authDropbox()
	{
        $auth = $this->getWebAuth();
        $authorizeUrl = $auth->start();

        //We must handly do it because dropbox don't work with Laravel's Session
        Session::put('dropbox-auth-csrf-token', $auth->getCsrfTokenStore()->get());

        return redirect($authorizeUrl);
	}

	public function callbackDropbox(Request $request)
	{
        try {
            list($accessToken, $userId, $urlState) = $this->getWebAuth()->finish($request->all());
            assert($urlState === null);  // Since we didn't pass anything in start()

            // We save $accessToken to make API requests.
            Auth::user()->accessTokenDropbox = $accessToken;
            Auth::user()->save();
        }
        catch (dbx\WebAuthException_BadRequest $ex) {
            Log::error("/dropbox-auth-finish: bad request: " . $ex->getMessage());
            // Respond with an HTTP 400 and display error page...
        }
        catch (dbx\WebAuthException_BadState $ex) {
            Log::info($ex->getMessage());
            // Auth session expired.  Restart the auth process.
            return redirect('/dropbox-auth-start');
        }
        catch (dbx\WebAuthException_Csrf $ex) {
            Log::error("/dropbox-auth-finish: CSRF mismatch: " . $ex->getMessage());
            // Respond with HTTP 403 and display error page...
        }
        catch (dbx\WebAuthException_NotApproved $ex) {
            Log::error("/dropbox-auth-finish: not approved: " . $ex->getMessage());
        }
        catch (dbx\WebAuthException_Provider $ex) {
            Log::error("/dropbox-auth-finish: error redirect from Dropbox: " . $ex->getMessage());
        }
        catch (dbx\Exception $ex) {
            Log::error("/dropbox-auth-finish: error communicating with Dropbox API: " . $ex->getMessage());
        }

        return redirect('/home');
	}

    private function getWebAuth()
    {
        //We must handly do it because Laravel don't work with $_SESSION
        $session = Session::all();

        $appInfo = new dbx\AppInfo(Config::get('clouds.dropbox.key'),
            Config::get('clouds.dropbox.secret'));
        $clientIdentifier = self::$clientIdentifier;
        $redirectUri = "https://multicloud.com/dropbox-auth-finish";
        $csrfTokenStore = new dbx\ArrayEntryStore($session, 'dropbox-auth-csrf-token');
        return new dbx\WebAuth($appInfo, $clientIdentifier, $redirectUri, $csrfTokenStore);
    }

    public function authYandex()
    {
        $client = new OAuthClient(Config::get('clouds.yandex_disk.id'));
        // сделать редирект и выйти
        $client->authRedirect(true);
        //Передать в запросе какое-то значение в параметре state, чтобы Yandex в ответе его вернул
        $state = 'yandex-php-library';
        $client->authRedirect(true, OAuthClient::CODE_AUTH_TYPE, $state);
    }

    public function callbackYandex()
    {
        $client = new OAuthClient(Config::get('clouds.yandex_disk.id'), Config::get('clouds.yandex_disk.password'));

        try {
            // осуществляем обмен
            $client->requestAccessToken($_REQUEST['code']);
        } catch (AuthRequestException $ex) {
            Log::warning($ex->getMessage());
        }

        // забираем полученный токен
        $token = $client->getAccessToken();

        Auth::user()->accessTokenYandex = $token;
        Auth::user()->save();

        // если вы передавали параметр state, то его можно получить в $_GET['state']

        return redirect('/home');
    }
}
