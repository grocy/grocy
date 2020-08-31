@extends('layout.default')

@section('title', $__t('Language'))
@section('activeNav', '')
@section('viewJsName', 'locale')

@section('content')
	<div class="row">
		<div class="col">
			<h2 class="title">@yield('title')</h2>
		</div>
	</div>
	<hr>
	<div class="row mt-3">
		<div class="col">
			<ul>
				@foreach($languages as $lang)
					<li>
						<label>
							<input name="language" type="radio" class="change-language" value="{{ $lang }}" @if(GROCY_LOCALE == $lang) checked @endif>
							<i class="flag-icon flag-icon-{{$lang}}"></i> <span
								class="txt-lang-name">{{ $__t($lang) }}</span>
						</label>
					</li>
				@endforeach
			</ul>
			<button id="locale-save" class="btn btn-success" type="submit">{{ $__t('Save') }}</button>
		</div>
	</div>
@endsection
