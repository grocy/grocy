@push('componentScripts')
	<script src="{{ $U('/viewjs/components/batterycard.js', true) }}?v={{ $version }}"></script>
@endpush

<div class="card">
	<div class="card-header">
		<i class="fas fa-battery-three-quarters"></i> {{ $__t('Battery overview') }}
		<a id="batterycard-battery-edit-button" class="btn btn-sm btn-outline-info py-0 float-right disabled" href="#" data-toggle="tooltip" title="{{ $__t('Edit battery') }}">
			<i class="fas fa-edit"></i>
		</a>
	</div>
	<div class="card-body">
		<h3><span id="batterycard-battery-name"></span></h3>
		<strong>{{ $__t('Used in') }}:</strong> <span id="batterycard-battery-used_in"></span><br>
		<strong>{{ $__t('Charge cycles count') }}:</strong> <span id="batterycard-battery-charge-cycles-count"></span><br>
		<strong>{{ $__t('Last charged') }}:</strong> <span id="batterycard-battery-last-charged"></span> <time id="batterycard-battery-last-charged-timeago" class="timeago timeago-contextual"></time><br>
	</div>
</div>
