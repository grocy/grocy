@push('componentScripts')
	<script src="/viewjs/components/habitcard.js"></script>
@endpush

<div class="main well">

	<h3>Habit overview <strong><span id="habitcard-habit-name"></span></strong></h3>

	<p>
		<strong>Tracked count:</strong> <span id="habitcard-habit-tracked-count"></span><br>
		<strong>Last tracked:</strong> <span id="habitcard-habit-last-tracked"></span> <time id="habitcard-habit-last-tracked-timeago" class="timeago timeago-contextual"></time><br>
	</p>
	
</div>
