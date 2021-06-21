<div class="card batterycard">
	<div class="card-header">
		<span class="float-left">{{ $__t('Battery overview') }}</span>
		<a id="batterycard-battery-edit-button"
			class="btn btn-sm btn-outline-secondary py-0 float-right disabled"
			href="#"
			data-toggle="tooltip"
			title="{{ $__t('Edit battery') }}">
			<i class="fas fa-edit"></i>
		</a>
		<a id="batterycard-battery-journal-button"
			class="btn btn-sm btn-outline-secondary py-0 mr-1 float-right disabled show-as-dialog-link"
			href="#">
			{{ $__t('Battery journal') }}
		</a>
	</div>
	<div class="card-body">
		<h3><span id="batterycard-battery-name"></span></h3>
		<strong>{{ $__t('Used in') }}:</strong> <span id="batterycard-battery-used_in"></span><br>
		<strong>{{ $__t('Charge cycles count') }}:</strong> <span id="batterycard-battery-charge-cycles-count"
			class="locale-number locale-number-generic"></span><br>
		<strong>{{ $__t('Last charged') }}:</strong> <span id="batterycard-battery-last-charged"></span> <time id="batterycard-battery-last-charged-timeago"
			class="timeago timeago-contextual"></time><br>
	</div>
</div>
