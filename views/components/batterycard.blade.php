@once
@push('componentScripts')
<script src="{{ $U('/viewjs/components/batterycard.js', true) }}?v={{ $version }}"></script>
@endpush
@endonce

@php if(!isset($asModal)) { $asModal = false; } @endphp

@if($asModal)
<div class="modal fade"
	id="batterycard-modal"
	tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content text-center">
			<div class="modal-body">
				@endif

				<div class="card batterycard">
					<div class="card-header">
						<span class="float-left">{{ $__t('Battery overview') }}</span>
						<a id="batterycard-battery-edit-button"
							class="btn btn-sm btn-outline-secondary py-0 float-right disabled"
							href="#"
							data-toggle="tooltip"
							title="{{ $__t('Edit battery') }}">
							<i class="fa-solid fa-edit"></i>
						</a>
						<a id="batterycard-battery-journal-button"
							class="btn btn-sm btn-outline-secondary py-0 mr-1 float-right disabled show-as-dialog-link"
							href="#"
							data-dialog-type="table">
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

				@if($asModal)
			</div>
			<div class="modal-footer">
				<button type="button"
					class="btn btn-secondary"
					data-dismiss="modal">{{ $__t('Close') }}</button>
			</div>
		</div>
	</div>
</div>
@endif
