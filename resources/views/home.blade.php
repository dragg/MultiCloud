@extends('app')

@section('content')
<div class="container">
	<div class="row">
		<div class="col-md-10 col-md-offset-1">
			<div class="panel panel-default">
				<div class="panel-heading">Home</div>

				<div class="panel-body">
					You are logged in!
				</div>
                <div class="panel-body">
                    <form action="{{action('Cloud\CloudAuthController@authDropbox')}}">
                        <button type="submit" class="btn btn-primary">Dropbox</button>
                    </form>
                    @foreach(Auth::user()->dropBoxes as $dropbox)
                        <form action="{{action('Cloud\DropboxController@show', ['id' => $dropbox->id])}}">
                            <button type="submit" class="btn btn-primary">Get my profile</button>
                        </form>

                        <form action="{{action('Cloud\DropboxController@destroy', ['id' => $dropbox->id])}}">
                            <button type="submit" class="btn btn-primary">Exit from dropbox</button>
                        </form>
                    @endforeach
                </div>
                <div class="panel-body">
                    <form action="{{action('Cloud\CloudAuthController@authYandex')}}">
                        <button type="submit" class="btn btn-primary">Yandex</button>
                    </form>
                    @foreach(Auth::user()->yandexDisks as $yDisk)
                        <form action="{{action('Cloud\YandexController@show', ['id' => $yDisk->id])}}">
                            <button type="submit" class="btn btn-primary">Get files</button>
                        </form>

                        <form action="{{action('Cloud\YandexController@destroy', ['id' => $yDisk->id])}}">
                            <button type="submit" class="btn btn-primary">Exit from yandex disk</button>
                        </form>
                    @endforeach
                </div>
                <div class="panel-body">
                    <form action="{{action('Cloud\CloudAuthController@authGoogle')}}">
                        <button type="submit" class="btn btn-primary">Google</button>
                    </form>
                    @foreach(Auth::user()->googleDrives as $gDrive)
                        <form action="{{action('Cloud\GoogleController@show', ['id' => $gDrive->id])}}">
                            <button type="submit" class="btn btn-primary">Get user info</button>
                        </form>

                        <form action="{{action('Cloud\GoogleController@destroy', ['id' => $gDrive->id])}}">
                            <button type="submit" class="btn btn-primary">Exit from google drive</button>
                        </form>
                    @endforeach
                </div>
			</div>
		</div>
	</div>
</div>
@endsection
