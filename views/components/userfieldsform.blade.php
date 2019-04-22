@push('componentScripts')
	<script src="{{ $U('/viewjs/components/userfieldsform.js', true) }}?v={{ $version }}"></script>
@endpush

@if(count($userfields) > 0)

<div id="userfields-form" data-entity="{{ $entity }}" class="border border-info p-2 mb-2" novalidate>
	<h2 class="small">{{ $L('Userfields') }}</h2>

	@foreach($userfields as $userfield)

	@if($userfield->type == \Grocy\Services\UserfieldsService::USERFIELD_TYPE_SINGLE_LINE_TEXT)
	<div class="form-group">
		<label for="name">{{ $userfield->caption }}</label>
		<input type="text" class="form-control userfield-input" id="{{ $userfield->name }}" value="">
	</div>
	@endif

	@if($userfield->type == \Grocy\Services\UserfieldsService::USERFIELD_TYPE_CHECKBOX)
	<div class="form-group">
		<div class="form-check">
			<input class="form-check-input userfield-input" type="checkbox" id="{{ $userfield->name }}" value="1">
			<label class="form-check-label" for="{{ $userfield->name }}">{{ $userfield->caption }}</label>
		</div>
	</div>
	@endif

	@endforeach

</div>

@endif
