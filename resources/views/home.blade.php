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
			</div>
		</div>
	</div>
</div>
@endsection
