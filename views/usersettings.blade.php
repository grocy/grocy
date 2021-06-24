@extends($rootLayout)

@section('title', $__t('User settings'))
@section('activeNav', '')
@section('viewJsName', 'usersettings')

@section('content')
<div class="row">
	<div class="col">
		<h2 class="title">@yield('title')</h2>
	</div>
</div>

<hr class="my-2">

<div class="row">
	<div class="col-lg-6 col-12">

		<div class="form-group">
			<label for="locale">{{ $__t('Language') }}</label>
			<select class="custom-control custom-select user-setting-control"
				id="locale"
				data-setting-key="locale">
				<option value="">{{ $__t('Default') }}</option>
				@foreach($languages as $lang)
				<option value="{{ $lang }}"
					@if(GROCY_LOCALE==$lang)
					checked
					@endif>{{ $__t($lang) }}</option>
				@endforeach
			</select>
		</div>

		<a href="{{ $U('/') }}"
			class="btn btn-success link-return">{{ $__t('OK') }}</a>
	</div>
</div>
@stop
