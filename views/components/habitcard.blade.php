@push('componentScripts')
	<script src="{{ $U('/viewjs/components/habitcard.js', true) }}?v={{ $version }}"></script>
@endpush

<div class="card">
	<div class="card-header">
		<i class="fa fa-fw fa-refresh"></i> {{ $L('Habit overview') }} <strong><span id="habitcard-habit-name"></span></strong>
	</div>
	<div class="card-body">
		<strong>{{ $L('Tracked count') }}:</strong> <span id="habitcard-habit-tracked-count"></span><br>
		<strong>{{ $L('Last tracked') }}:</strong> <span id="habitcard-habit-last-tracked"></span> <time id="habitcard-habit-last-tracked-timeago" class="timeago timeago-contextual"></time><br>
	</div>
</div>
