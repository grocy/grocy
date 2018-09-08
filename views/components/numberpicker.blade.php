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

<div class="form-group {{ $additionalGroupCssClasses }}">
	<label for="{{ $id }}">{{ $L($label) }}&nbsp;&nbsp;<span id="{{ $hintId }}" class="small text-muted">{{ $hint }}</span></label>
	<div class="input-group">
		<input {!! $additionalAttributes !!} type="number" class="form-control numberpicker {{ $additionalCssClasses }}" id="{{ $id }}" name="{{ $id }}" value="{{ $value }}" min="{{ $min }}" max="{{ $max }}" step="{{ $step }}" required>
		<div class="input-group-append"">
			<div class="input-group-text numberpicker-up-button"><i class="fas fa-arrow-up"></i></div>
		</div>
		<div class="input-group-append"">
			<div class="input-group-text numberpicker-down-button"><i class="fas fa-arrow-down"></i></div>
		</div>
		<div class="invalid-feedback">{{ $invalidFeedback }}</div>
	</div>
</div>
