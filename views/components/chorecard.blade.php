<div class="card chorecard">
	<div class="card-header">
		<span class="float-left">{{ $__t('Chore overview') }}</span>
		<a id="chorecard-chore-edit-button"
			class="btn btn-sm btn-outline-secondary py-0 float-right disabled"
			href="#"
			data-toggle="tooltip"
			title="{{ $__t('Edit chore') }}">
			<i class="fas fa-edit"></i>
		</a>
		<a id="chorecard-chore-journal-button"
			class="btn btn-sm btn-outline-secondary py-0 mr-1 float-right disabled show-as-dialog-link"
			href="#">
			{{ $__t('Chore journal') }}
		</a>
	</div>
	<div class="card-body">
		<h3><span id="chorecard-chore-name"></span></h3>
		<strong>{{ $__t('Tracked count') }}:</strong> <span id="chorecard-chore-tracked-count"
			class="locale-number locale-number-generic"></span><br>
		<strong>{{ $__t('Last tracked') }}:</strong> <span id="chorecard-chore-last-tracked"></span> <time id="chorecard-chore-last-tracked-timeago"
			class="timeago timeago-contextual"></time><br>
		@if(GROCY_FEATURE_FLAG_CHORES_ASSIGNMENTS)
		<strong>{{ $__t('Last done by') }}:</strong> <span id="chorecard-chore-last-done-by"></span>
		@endif
	</div>
</div>
