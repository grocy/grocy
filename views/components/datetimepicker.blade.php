@push('componentScripts')
	<script src="{{ $U('/viewjs/components/datetimepicker.js', true) }}?v={{ $version }}"></script>
@endpush

<div class="form-group">
	<label for="{{ $id }}">{{ $L($label) }}&nbsp;&nbsp;<span class="small text-muted"><time id="datetimepicker-timeago" class="timeago timeago-contextual"></time>@if(!empty($hint))<br>{{ $L($hint) }}@endif</span></label>
	<div class="input-group date datetimepicker" id="{{ $id }}" data-target-input="nearest">
		<input type="text" required class="form-control datetimepicker-input"
			data-target="#{{ $id }}" data-format="{{ $format }}"
			data-init-with-now="{{ BoolToString($initWithNow) }}"
			data-limit-end-to-now="{{ BoolToString($limitEndToNow) }}"
			data-limit-start-to-now="{{ BoolToString($limitStartToNow) }}"
			data-next-input-selector="{{ $nextInputSelector }}" />
		<div class="input-group-append" data-target="#{{ $id }}" data-toggle="datetimepicker">
			<div class="input-group-text"><i class="fas fa-calendar"></i></div>
		</div>
		<div class="invalid-feedback">{{ $invalidFeedback }}</div>
	</div>
</div>
