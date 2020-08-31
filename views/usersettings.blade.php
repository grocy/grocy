@extends('layout.default')

@section('title', $__t('User settings'))
@section('activeNav', '')
@section('viewJsName', 'usersettings')

@section('content')
<div class="row">
	<div class="col">
		<h2 class="title">@yield('title')</h2>
		<hr>
	</div>
</div>
<div class="row">
	<div class="col-lg-6 col-xs-12">

		<div class="form-group">
			<label for="locale">{{ $__t('Language') }}</label>
			<select class="form-control user-setting-control" id="locale" data-setting-key="locale">
				<option value="">{{ $__t('Default') }}</option>
				@foreach($languages as $lang)
					<option value="{{ $lang }}" @if(GROCY_LOCALE == $lang) checked @endif>{{ $__t($lang) }}</option>
				@endforeach
			</select>
		</div>

		<a href="{{ $U('/') }}" class="btn btn-success">{{ $__t('OK') }}</a>
	</div>
</div>
@stop
