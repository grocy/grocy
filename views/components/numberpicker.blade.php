@push('componentScripts')
	<script src="{{ $U('/viewjs/components/numberpicker.js', true) }}?v={{ $version }}"></script>
@endpush

@php if(!isset($value)) { $value = 1; } @endphp
@php if(empty($min)) { $min = 0; } @endphp
@php if(empty($max)) { $max = 999999; } @endphp
@php if(empty($step)) { $step = 1; } @endphp
@php if(empty($hint)) { $hint = ''; } @endphp
@php if(empty($hintId)) { $hintId = ''; } @endphp
@php if(empty($additionalCssClasses)) { $additionalCssClasses = ''; } @endphp
@php if(empty($additionalGroupCssClasses)) { $additionalGroupCssClasses = ''; } @endphp
@php if(empty($additionalAttributes)) { $additionalAttributes = ''; } @endphp
@php if(empty($additionalHtmlElements)) { $additionalHtmlElements = ''; } @endphp
@php if(empty($additionalHtmlContextHelp)) { $additionalHtmlContextHelp = ''; } @endphp
@php if(!isset($isRequired)) { $isRequired = true; } @endphp
@php if(!isset($noNameAttribute)) { $noNameAttribute = false; } @endphp

<div class="form-group {{ $additionalGroupCssClasses }}">
	<label for="{{ $id }}">
		{{ $__t($label) }}&nbsp;
		<i class="fas fa-question-circle" id="{{ $hintId }}" data-toggle="tooltip" title="{{ $hint }}"></i>{!! $additionalHtmlContextHelp !!}</label>
	<div class="input-group">
		<input {!! $additionalAttributes !!} type="number" class="form-control numberpicker {{ $additionalCssClasses }}" id="{{ $id }}" @if(!$noNameAttribute) name="{{ $id }}" @endif value="{{ $value }}" min="{{ $min }}" max="{{ $max }}" step="{{ $step }}" @if($isRequired) required @endif>
		<div class="input-group-append">
			<div class="input-group-text numberpicker-up-button"><i class="fas fa-arrow-up"></i></div>
		</div>
		<div class="input-group-append">
			<div class="input-group-text numberpicker-down-button"><i class="fas fa-arrow-down"></i></div>
		</div>
		<div class="invalid-feedback">{{ $invalidFeedback }}</div>
	</div>
	{!! $additionalHtmlElements !!}
</div>
