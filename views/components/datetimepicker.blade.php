@push('componentScripts')
	<script src="{{ $U('/viewjs/components/datetimepicker.js', true) }}?v={{ $version }}"></script>
@endpush

<div class="form-group">
	<label for="{{ $id }}">{{ $L($label) }}</label>
	<div class="input-group date datetimepicker" id="{{ $id }}" data-target-input="nearest">
		<input type="text" class="form-control datetimepicker-input" data-target="#{{ $id }}"/>
		<div class="input-group-append" data-target="#{{ $id }}" data-toggle="datetimepicker">
			<div class="input-group-text"><i class="fa fa-calendar"></i></div>
		</div>
	</div>
	<div class="invalid-feedback"></div>
</div>
