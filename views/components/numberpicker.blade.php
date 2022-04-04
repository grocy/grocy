@once
@push('componentScripts')
<script src="{{ $U('/viewjs/components/numberpicker.js', true) }}?v={{ $version }}"></script>
@endpush
@endonce

@php if(!isset($value)) { $value = 1; } @endphp
@php if(empty($min)) { $min = 0; } @endphp
@php if(!isset($max)) { $max = ''; } @endphp
@php if(empty($decimals)) { $decimals = 0; } @endphp
@php if(empty($hint)) { $hint = ''; } @endphp
@php if(empty($hintId)) { $hintId = ''; } @endphp
@php if(empty($additionalCssClasses)) { $additionalCssClasses = ''; } @endphp
@php if(empty($additionalGroupCssClasses)) { $additionalGroupCssClasses = ''; } @endphp
@php if(empty($additionalAttributes)) { $additionalAttributes = ''; } @endphp
@php if(empty($additionalHtmlElements)) { $additionalHtmlElements = ''; } @endphp
@php if(empty($additionalHtmlContextHelp)) { $additionalHtmlContextHelp = ''; } @endphp
@php if(!isset($isRequired)) { $isRequired = true; } @endphp
@php if(!isset($noNameAttribute)) { $noNameAttribute = false; } @endphp
@php if(empty($contextInfoId)) { $additionalHtmlContextHelp = ''; } @endphp
@php if(!isset($invalidFeedback)) { $invalidFeedback = ''; } @endphp

<div id="group-{{ $id }}"
	class="form-group {{ $additionalGroupCssClasses }}">
	<label class="w-100"
		for="{{ $id }}">
		{{ $__t($label) }}
		@if(!empty($hint) || !empty($hintId))
		<i id="{{ $hintId }}"
			class="fa-solid fa-question-circle text-muted"
			data-toggle="tooltip"
			data-trigger="hover click"
			title="{{ $hint }}"></i>
		@endif
		{!! $additionalHtmlContextHelp !!}
		@if(!empty($contextInfoId))
		<span id="{{ $contextInfoId }}"
			class="small text-muted float-right mt-1"></span>
		@endif
	</label>
	<div class="input-group">
		<input {!!
			$additionalAttributes
			!!}
			type="number"
			class="form-control numberpicker {{ $additionalCssClasses }}"
			id="{{ $id }}"
			@if(!$noNameAttribute)
			name="{{ $id }}"
			@endif
			value="{{ $value }}"
			min="{{ number_format($min, $decimals, '.', '') }}"
			@if(!empty($max))
			max="{{ number_format($max, $decimals, '.', '') }}"
			@endif
			step="@if($decimals <= 0){{1}}@else{{'.' . str_repeat('0', $decimals - 1) . '1'}}@endif"
			data-decimals="{{ $decimals }}"
			@if($isRequired)
			required
			@endif
			autocomplete="off">
		<div class="input-group-append">
			<div class="input-group-text numberpicker-up-button"><i class="fa-solid fa-arrow-up"></i></div>
		</div>
		<div class="input-group-append">
			<div class="input-group-text numberpicker-down-button"><i class="fa-solid fa-arrow-down"></i></div>
		</div>
		<div class="invalid-feedback">{{ $invalidFeedback }}</div>
	</div>
	{!! $additionalHtmlElements !!}
</div>
