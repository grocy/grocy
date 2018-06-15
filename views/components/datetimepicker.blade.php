@push('componentScripts')
	<script src="{{ $U('/viewjs/components/datetimepicker.js', true) }}?v={{ $version }}"></script>
@endpush

<div class="form-group">
<label for="{{ $id }}">{{ $L($label) }}</label>
	<div class="input-group date datetimepicker">
		<input type="text" class="form-control" id="{{ $id }}" name="{{ $id }}" required>
		<span class="input-group-addon">
			<span class="fa fa-calendar"></span>
		</span>
	</div>
	<div class="help-block with-errors"></div>
</div>
