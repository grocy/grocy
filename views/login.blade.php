@extends($rootLayout)

@section('title', $__t('Login'))
@section('viewJsName', 'login')

@section('content')
<div class="row">
	<div class="col-lg-6 offset-lg-3 col-12">
		<h2 class="text-center">@yield('title')</h2>

		<hr class="my-2">

		<form method="post"
			action="{{ $U('/login') }}"
			id="login-form"
			novalidate>

			<div class="form-group">
				<label for="name">{{ $__t('Username') }}</label>
				<input type="text"
					class="form-control"
					required
					id="username"
					name="username">
			</div>

			<div class="form-group">
				<label for="name">{{ $__t('Password') }}</label>
				<input type="password"
					class="form-control"
					required
					id="password"
					name="password">
				<div id="login-error"
					class="form-text text-danger d-none"></div>
			</div>

			<div class="form-group mt-n2">
				<div class="custom-control custom-checkbox">
					<input type="checkbox"
						class="form-check-input custom-control-input"
						id="stay_logged_in">
					<label class="form-check-label custom-control-label"
						for="stay_logged_in">
						{{ $__t('Stay logged in permanently') }}
						<i class="fas fa-question-circle text-muted"
							data-toggle="tooltip"
							title="{{ $__t('When not set, you will get logged out at latest after 30 days') }}"></i>
					</label>
				</div>
			</div>

			<button id="login-button"
				class="btn btn-success">{{ $__t('OK') }}</button>

		</form>
	</div>
</div>
@stop
