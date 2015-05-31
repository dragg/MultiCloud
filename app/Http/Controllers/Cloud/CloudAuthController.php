<?php namespace App\Http\Controllers\Cloud;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Dropbox as dbx;
use \Auth;
use \Config;
use Illuminate\Http\Request;
use \Log;
use \Session;
use Yandex\OAuth\OAuthClient;
use Yandex\OAuth\Exception\AuthRequestException;
use Google_Client;
use Google_Service_Drive;
use App\Services\DropBoxService;
use App\Services\YandexDiskService;
use App\Services\GoogleDriveService;

class CloudAuthController extends Controller {

    private static $clientIdentifier = "MultiCloudThesis alpha";

	public function getDropboxAuthStart(Request $request)
	{
        Session::put('name', $request->get('name'));

        $auth = $this->getWebAuth();
        $authorizeUrl = $auth->start();

        //We must handly do it because dropbox don't work with Laravel's Session
        Session::put('dropbox-auth-csrf-token', $auth->getCsrfTokenStore()->get());

        return redirect($authorizeUrl);
	}

	public function getDropboxAuthFinish(Request $request)
	{
        try {
            list($accessToken, $userId, $urlState) = $this->getWebAuth()->finish($request->all());
            assert($urlState === null);  // Since we didn't pass anything in start()

            // We save $accessToken to make API requests.
            $dropbox = new DropBoxService();
            $name = Session::get('name'); Session::forget('name');
            $dropbox->create(['access_token' => $accessToken, 'user_id' => Auth::user()->id, 'name' => $name]);
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
            Log::error("/dropbox-auth-finish: error redirect from DropBox: " . $ex->getMessage());
        }
        catch (dbx\Exception $ex) {
            Log::error("/dropbox-auth-finish: error communicating with DropBox API: " . $ex->getMessage());
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

    public function getYandexAuthStart(Request $request)
    {
        $client = new OAuthClient(Config::get('clouds.yandex_disk.id'));
        //Передать в запросе какое-то значение в параметре state, чтобы Yandex в ответе его вернул
        $state = $request->get('name');
        // сделать редирект и выйти
        $client->authRedirect(true, OAuthClient::CODE_AUTH_TYPE, $state);
    }

    public function getYandexAuthFinish(Request $request)
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

        $yandex = new YandexDiskService();
        $name = $request->get('state');
        $yandex->create(['access_token' => $token, 'user_id' => Auth::user()->id, 'name' => $name]);

        return redirect('/home');
    }

    public function getGoogleAuthStart(Request $request)
    {
        Session::put('name', $request->get('name'));

        $client = new Google_Client();

        //$client->setApplicationName(Config::get('clouds.google_drive.name'));
        $client->setClientSecret(Config::get('clouds.google_drive.secret'));
        $client->setClientId(Config::get('clouds.google_drive.id'));
        $client->addScope([
            \Google_Service_Oauth2::USERINFO_PROFILE ,
            \Google_Service_Oauth2::USERINFO_EMAIL,
            \Google_Service_Drive::DRIVE,
            \Google_Service_Drive::DRIVE_FILE,
            \Google_Service_Drive::DRIVE_APPDATA,
            \Google_Service_Drive::DRIVE_SCRIPTS,
            \Google_Service_Drive::DRIVE_APPS_READONLY,
            "https://www.googleapis.com/auth/drive.metadata"

        ]);
        $client->setRedirectUri(Config::get('app.url') . '/google-auth-finish');
        $url = $client->createAuthUrl();

        return redirect($url);
    }

    public function getGoogleAuthFinish(Request $request)
    {
        if($request->exists('code')) {
            $client = new Google_Client();
            $client->setClientSecret(Config::get('clouds.google_drive.secret'));
            $client->setClientId(Config::get('clouds.google_drive.id'));
            $client->setRedirectUri(Config::get('app.url') . '/google-auth-finish');
            $accessTokenJSON = $client->authenticate($request->get('code'));

            $googleService = new GoogleDriveService();
            $token = json_decode($accessTokenJSON);
            $name = Session::get('name'); Session::forget('name');
            $googleService->create(array_merge((array)$token, ['user_id' => Auth::user()->id, 'name' => $name]));
        }

        return redirect('/home');
    }
}
