@extends('layout.default')

@if($mode == 'edit')
	@section('title', $__t('Edit user'))
@else
	@section('title', $__t('Create user'))
@endif

@section('viewJsName', 'userform')

@section('content')
<div class="row">
	<div class="col">
		<h2 class="title">@yield('title')</h2>
		<hr>
	</div>
</div>
<div class="row">
	<div class="col-lg-6 col-xs-12">
		<script>Grocy.EditMode = '{{ $mode }}';</script>

		@if($mode == 'edit')
			<script>Grocy.EditObjectId = {{ $user->id }};</script>
		@endif

		<form id="user-form" novalidate>

			<div class="form-group">
				<label for="username">{{ $__t('Username') }}</label>
				<input type="text" class="form-control" required id="username" name="username" value="@if($mode == 'edit'){{ $user->username }}@endif">
				<div class="invalid-feedback">{{ $__t('A username is required') }}</div>
			</div>

			<div class="form-group">
				<label for="first_name">{{ $__t('First name') }}</label>
				<input type="text" class="form-control" id="first_name" name="first_name" value="@if($mode == 'edit'){{ $user->first_name }}@endif">
			</div>

			<div class="form-group">
				<label for="last_name">{{ $__t('Last name') }}</label>
				<input type="text" class="form-control" id="last_name" name="last_name" value="@if($mode == 'edit'){{ $user->last_name }}@endif">
			</div>

			<div class="form-group">
				<label for="password">{{ $__t('Password') }}</label>
				<input type="password" class="form-control" required id="password" name="password">
			</div>

			<div class="form-group">
				<label for="password_confirm">{{ $__t('Confirm password') }}</label>
				<input type="password" class="form-control" required id="password_confirm" name="password_confirm">
				<div class="invalid-feedback">{{ $__t('Passwords do not match') }}</div>
			</div>

			<button id="save-user-button" class="btn btn-success">{{ $__t('Save') }}</button>

		</form>
	</div>
</div>
@stop
