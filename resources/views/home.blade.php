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
                    @if(Auth::user()->accessTokenDropbox != null)
                        <form action="{{action('Cloud\DropboxController@index')}}">
                            <button type="submit" class="btn btn-primary">Get my profile</button>
                        </form>

                        <form action="{{action('Cloud\DropboxController@destroy')}}">
                            <button type="submit" class="btn btn-primary">Exit from dropbox</button>
                        </form>
                    @endif
                </div>
                <div class="panel-body">
                    <form action="{{action('Cloud\CloudAuthController@authYandex')}}">
                        <button type="submit" class="btn btn-primary">Yandex</button>
                    </form>
                    @if(Auth::user()->accessTokenYandex != null)
                        <form action="{{action('Cloud\YandexController@index')}}">
                            <button type="submit" class="btn btn-primary">Get my root files</button>
                        </form>

                        <form action="{{action('Cloud\YandexController@destroy')}}">
                            <button type="submit" class="btn btn-primary">Exit from yandex</button>
                        </form>
                    @endif
                </div>
                <div class="panel-body">
                    <form action="{{action('Cloud\CloudAuthController@authGoogle')}}">
                        <button type="submit" class="btn btn-primary">Google</button>
                    </form>
                    @if(Auth::user()->accessTokenGoogle != null)
                        <form action="{{action('Cloud\GoogleController@index')}}">
                            <button type="submit" class="btn btn-primary">Get my root files</button>
                        </form>

                        <form action="{{action('Cloud\GoogleController@destroy')}}">
                            <button type="submit" class="btn btn-primary">Exit from google</button>
                        </form>
                    @endif
                </div>
			</div>
		</div>
	</div>
</div>
@endsection
