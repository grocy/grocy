@php require_frontend_packages(['tempusdominus']); @endphp

@once
@push('componentScripts')
<script src="{{ $U('/viewjs/components/datetimepicker2.js', true) }}?v={{ $version }}"></script>
@endpush
@endonce

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

<div class="datetimepicker2-wrapper form-group {{ $additionalGroupCssClasses }}">
	<label for="{{ $id }}">{{ $__t($label) }}
		@if(!empty($hint))
		&nbsp;<i class="fa-solid fa-question-circle text-muted"
			data-toggle="tooltip"
			data-trigger="hover click"
			title="{{ $hint }}"></i>
		@endif
		<span class="small text-muted">
			<time id="datetimepicker2-timeago"
				class="timeago timeago-contextual"></time>
		</span>
	</label>
	<div class="input-group">
		<div class="input-group date datetimepicker2 @if(!empty($additionalGroupCssClasses)){{ $additionalGroupCssClasses }}@endif"
			id="{{ $id }}"
			@if(!$noNameAttribute)
			name="{{ $id }}"
			@endif
			data-target-input="nearest">
			<input {!!
				$additionalAttributes
				!!}
				type="text"
				@if($activateNumberPad)
				inputmode="numeric"
				@endif
				@if($isRequired)
				required
				@endif
				class="form-control datetimepicker2-input @if(!empty($additionalCssClasses)){{ $additionalCssClasses }}@endif"
				data-target="#{{ $id }}"
				data-format="{{ $format }}"
				data-init-with-now="{{ BoolToString($initWithNow) }}"
				data-init-value="{{ $initialValue }}"
				data-limit-end-to-now="{{ BoolToString($limitEndToNow) }}"
				data-limit-start-to-now="{{ BoolToString($limitStartToNow) }}"
				data-next-input-selector="{{ $nextInputSelector }}"
				data-earlier-than-limit="{{ $earlierThanInfoLimit }}" />
			<div class="input-group-append"
				data-target="#{{ $id }}"
				data-toggle="datetimepicker">
				<div class="input-group-text"><i class="fa-solid fa-calendar"></i></div>
			</div>
			<div class="invalid-feedback">{{ $invalidFeedback }}</div>
		</div>
		<div id="datetimepicker2-earlier-than-info"
			class="form-text text-info font-italic w-100 d-none">{{ $earlierThanInfoText }}</div>
		@if(isset($shortcutValue) && isset($shortcutLabel))
		<div class="form-group mt-n2 mb-0>
			<div class="
			custom-control
			custom-checkbox">
			<input class="form-check-input custom-control-input"
				type="checkbox"
				id="datetimepicker2-shortcut"
				name="datetimepicker2-shortcut"
				value="1"
				data-datetimepicker2-shortcut-value="{{ $shortcutValue }}"
				tabindex="-1">
			<label class="form-check-label custom-control-label"
				for="datetimepicker2-shortcut">{{ $__t($shortcutLabel) }}
			</label>
		</div>
	</div>
	@endif
</div>
</div>
