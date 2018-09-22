@push('componentScripts')
	<script src="{{ $U('/viewjs/components/chorecard.js', true) }}?v={{ $version }}"></script>
@endpush

<div class="card">
	<div class="card-header">
		<i class="fas fa-refresh"></i> {{ $L('Chore overview') }}
	</div>
	<div class="card-body">
		<h3><span id="chorecard-chore-name"></span></h3>
		<strong>{{ $L('Tracked count') }}:</strong> <span id="chorecard-chore-tracked-count"></span><br>
		<strong>{{ $L('Last tracked') }}:</strong> <span id="chorecard-chore-last-tracked"></span> <time id="chorecard-chore-last-tracked-timeago" class="timeago timeago-contextual"></time><br>
		<strong>{{ $L('Last done by') }}:</strong> <span id="chorecard-chore-last-done-by"></span>
	</div>
</div>
