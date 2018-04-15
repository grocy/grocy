@push('componentScripts')
	<script src="/viewjs/components/batterycard.js"></script>
@endpush

<div class="main well">

	<h3>Battery overview <strong><span id="batterycard-battery-name"></span></strong></h3>

	<p>
		<strong>Charge cycles count:</strong> <span id="batterycard-battery-charge-cycles-count"></span><br>
		<strong>Last charged:</strong> <span id="batterycard-battery-last-charged"></span> <time id="batterycard-battery-last-charged-timeago" class="timeago timeago-contextual"></time><br>
	</p>
	
</div>
