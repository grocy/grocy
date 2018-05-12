@extends('layout.default')

@section('title', $L('Login'))
@section('viewJsName', 'login')

@section('content')
	<div class="col-md-6 col-md-offset-3 col-xs-12">
		<h1 class="page-header text-center">@yield('title')</h1>

		<form method="post" action="{{ $U('/login') }}" id="login-form">

			<div class="form-group">
				<label for="name">{{ $L('Username') }}</label>
				<input type="text" class="form-control" required id="username" name="username">
				<div class="help-block with-errors"></div>
			</div>

			<div class="form-group">
				<label for="name">{{ $L('Password') }}</label>
				<input type="password" class="form-control" required id="password" name="password">
				<div id="login-error" class="help-block with-errors"></div>
			</div>

			<button id="login-button" type="submit" class="btn btn-default">{{ $L('OK') }}</button>

		</form>
	</div>
@stop
