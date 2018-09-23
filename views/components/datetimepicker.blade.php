@push('componentScripts')
	<script src="{{ $U('/viewjs/components/datetimepicker.js', true) }}?v={{ $version }}"></script>
@endpush

@php if(!isset($isRequired)) { $isRequired = true; } @endphp
@php if(!isset($initialValue)) { $initialValue = ''; } @endphp

<div class="form-group">
	<label for="{{ $id }}">{{ $L($label) }}  <span class="small text-muted"><time id="datetimepicker-timeago" class="timeago timeago-contextual"></time>@if(!empty($hint))<br>{{ $L($hint) }}@endif</span></label>
	<div class="input-group">
		<div class="input-group date datetimepicker @if(!empty($additionalCssClasses)){{ $additionalCssClasses }}@endif" id="{{ $id }}" data-target-input="nearest">
			<input type="text" @if($isRequired) required @endif class="form-control datetimepicker-input"
				data-target="#{{ $id }}" data-format="{{ $format }}"
				data-init-with-now="{{ BoolToString($initWithNow) }}"
				data-init-value="{{ $initialValue }}"
				data-limit-end-to-now="{{ BoolToString($limitEndToNow) }}"
				data-limit-start-to-now="{{ BoolToString($limitStartToNow) }}"
				data-next-input-selector="{{ $nextInputSelector }}" />
			<div class="input-group-append" data-target="#{{ $id }}" data-toggle="datetimepicker">
				<div class="input-group-text"><i class="fas fa-calendar"></i></div>
			</div>
		</div>
		@if(isset($shortcutValue) && isset($shortcutLabel))
		<div class="form-check w-100">
			<input class="form-check-input" type="checkbox" id="datetimepicker-shortcut" data-datetimepicker-shortcut-value="{{ $shortcutValue }}">
			<label class="form-check-label" for="datetimepicker-shortcut">{{ $L($shortcutLabel) }}</label>
		</div>
		@endif
		<div class="invalid-feedback">{{ $invalidFeedback }}</div>
	</div>
</div>
