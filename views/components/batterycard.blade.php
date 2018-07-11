@push('componentScripts')
	<script src="{{ $U('/viewjs/components/batterycard.js', true) }}?v={{ $version }}"></script>
@endpush

<div class="card">
	<div class="card-header">
		<i class="fas fa-battery-three-quarters"></i> {{ $L('Battery overview') }}
	</div>
	<div class="card-body">
		<h3><span id="batterycard-battery-name"></span></h3>
		<strong>{{ $L('Used in') }}:</strong> <span id="batterycard-battery-used_in"></span><br>
		<strong>{{ $L('Charge cycles count') }}:</strong> <span id="batterycard-battery-charge-cycles-count"></span><br>
		<strong>{{ $L('Last charged') }}:</strong> <span id="batterycard-battery-last-charged"></span> <time id="batterycard-battery-last-charged-timeago" class="timeago timeago-contextual"></time><br>
	</div>
</div>
