@push('componentScripts')
	<script src="{{ $U('/viewjs/components/chorecard.js', true) }}?v={{ $version }}"></script>
@endpush

<div class="card">
	<div class="card-header">
		<i class="fas fa-home"></i> {{ $__t('Chore overview') }}
		<a id="chorecard-chore-edit-button" class="btn btn-sm btn-outline-info py-0 float-right disabled" href="#" data-toggle="tooltip" title="{{ $__t('Edit chore') }}">
			<i class="fas fa-edit"></i>
		</a>
	</div>
	<div class="card-body">
		<h3><span id="chorecard-chore-name"></span></h3>
		<strong>{{ $__t('Tracked count') }}:</strong> <span id="chorecard-chore-tracked-count"></span><br>
		<strong>{{ $__t('Last tracked') }}:</strong> <span id="chorecard-chore-last-tracked"></span> <time id="chorecard-chore-last-tracked-timeago" class="timeago timeago-contextual"></time><br>
		<strong>{{ $__t('Last done by') }}:</strong> <span id="chorecard-chore-last-done-by"></span>
	</div>
</div>
