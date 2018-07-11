@push('componentScripts')
	<script src="{{ $U('/viewjs/components/habitcard.js', true) }}?v={{ $version }}"></script>
@endpush

<div class="card">
	<div class="card-header">
		<i class="fas fa-refresh"></i> {{ $L('Habit overview') }}
	</div>
	<div class="card-body">
		<h3><span id="habitcard-habit-name"></span></h3>
		<strong>{{ $L('Tracked count') }}:</strong> <span id="habitcard-habit-tracked-count"></span><br>
		<strong>{{ $L('Last tracked') }}:</strong> <span id="habitcard-habit-last-tracked"></span> <time id="habitcard-habit-last-tracked-timeago" class="timeago timeago-contextual"></time><br>
	</div>
</div>
