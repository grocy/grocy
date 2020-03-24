@push('componentScripts')
	<script src="{{ $U('/viewjs/components/datetimepicker.js', true) }}?v={{ $version }}"></script>
@endpush

@php if(!isset($isRequired)) { $isRequired = true; } @endphp
@php if(!isset($initialValue)) { $initialValue = ''; } @endphp
@php if(empty($earlierThanInfoLimit)) { $earlierThanInfoLimit = ''; } @endphp
@php if(empty($earlierThanInfoText)) { $earlierThanInfoText = ''; } @endphp
@php if(empty($additionalCssClasses)) { $additionalCssClasses = ''; } @endphp
@php if(empty($additionalGroupCssClasses)) { $additionalGroupCssClasses = ''; } @endphp
@php if(empty($invalidFeedback)) { $invalidFeedback = ''; } @endphp
@php if(!isset($isRequired)) { $isRequired = true; } @endphp
@php if(!isset($noNameAttribute)) { $noNameAttribute = false; } @endphp
@php if(!isset($nextInputSelector)) { $nextInputSelector = false; } @endphp
@php if(empty($additionalAttributes)) { $additionalAttributes = ''; } @endphp
@php if(empty($additionalGroupCssClasses)) { $additionalGroupCssClasses = ''; } @endphp
@php if(empty($activateNumberPad)) { $activateNumberPad = false; } @endphp

<div id="datetimepicker-wrapper" class="form-group {{ $additionalGroupCssClasses }}">
	<label for="{{ $id }}">{{ $__t($label) }}
		<span class="small text-muted">
			@if(!empty($hint)){{ $__t($hint) }}@endif
			<time id="datetimepicker-timeago" class="timeago timeago-contextual"></time>
		</span>
	</label>
	<div class="input-group">
		<div class="input-group date datetimepicker @if(!empty($additionalGroupCssClasses)){{ $additionalGroupCssClasses }}@endif" id="{{ $id }}" @if(!$noNameAttribute) name="{{ $id }}" @endif data-target-input="nearest">
			<input {!! $additionalAttributes !!} type="text" @if($activateNumberPad) inputmode="numeric" @endif @if($isRequired) @if($isRequired) required @endif @endif class="form-control datetimepicker-input @if(!empty($additionalCssClasses)){{ $additionalCssClasses }}@endif"
				data-target="#{{ $id }}" data-format="{{ $format }}"
				data-init-with-now="{{ BoolToString($initWithNow) }}"
				data-init-value="{{ $initialValue }}"
				data-limit-end-to-now="{{ BoolToString($limitEndToNow) }}"
				data-limit-start-to-now="{{ BoolToString($limitStartToNow) }}"
				data-next-input-selector="{{ $nextInputSelector }}"
				data-earlier-than-limit="{{ $earlierThanInfoLimit }}" />
			<div class="input-group-append" data-target="#{{ $id }}" data-toggle="datetimepicker">
				<div class="input-group-text"><i class="fas fa-calendar"></i></div>
			</div>
			<div class="invalid-feedback">{{ $invalidFeedback }}</div>
		</div>
		<div id="datetimepicker-earlier-than-info" class="form-text text-info font-italic d-none">{{ $earlierThanInfoText }}</div>
		@if(isset($shortcutValue) && isset($shortcutLabel))
		<div class="form-check w-100">
			<input class="form-check-input" type="checkbox" id="datetimepicker-shortcut" data-datetimepicker-shortcut-value="{{ $shortcutValue }}">
			<label class="form-check-label" for="datetimepicker-shortcut">{{ $__t($shortcutLabel) }}</label>
		</div>
		@endif
	</div>
</div>
