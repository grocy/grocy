@push('componentScripts')
	<script src="{{ $U('/viewjs/components/habitcard.js') }}?v={{ $version }}"></script>
@endpush

<div class="main well">

	<h3>{{ $L('Habit overview') }} <strong><span id="habitcard-habit-name"></span></strong></h3>

	<p>
		<strong>{{ $L('Tracked count') }}:</strong> <span id="habitcard-habit-tracked-count"></span><br>
		<strong>{{ $L('Last tracked') }}:</strong> <span id="habitcard-habit-last-tracked"></span> <time id="habitcard-habit-last-tracked-timeago" class="timeago timeago-contextual"></time><br>
	</p>
	
</div>
