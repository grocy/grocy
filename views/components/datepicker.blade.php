@push('componentScripts')
	<script src="{{ $U('/viewjs/components/datepicker.js', true) }}?v={{ $version }}"></script>
@endpush

<div class="form-group">
	<label for="{{ $id }}">{{ $L($label) }}&nbsp;&nbsp;<span class="small text-muted"><time id="datepicker-timeago" class="timeago timeago-contextual"></time>@if(!empty($hint))<br>{{ $L($hint) }}@endif</span></label>
	<div class="input-group date">
		<input type="text" data-isodate="isodate" class="form-control datepicker" id="{{ $id }}" name="{{ $id }}" required autocomplete="off" min="2018-07-11">
		<div id="datepicker-button" class="input-group-append">
			<div class="input-group-text"><i class="fas fa-calendar"></i></div>
		</div>
	</div>
	<div class="invalid-feedback"></div>
</div>
