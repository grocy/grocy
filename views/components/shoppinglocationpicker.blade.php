@php require_frontend_packages(['bootstrap-combobox']); @endphp

@once
@push('componentScripts')
<script src="{{ $U('/viewjs/components/shoppinglocationpicker.js', true) }}?v={{ $version }}"></script>
@endpush
@endonce

@php if(empty($prefillByName)) { $prefillByName = ''; } @endphp
@php if(empty($prefillById)) { $prefillById = ''; } @endphp
@php if(!isset($isRequired)) { $isRequired = false; } @endphp
@php if(empty($hint)) { $hint = ''; } @endphp
@php if(empty($nextInputSelector)) { $nextInputSelector = ''; } @endphp

<div class="form-group"
	data-next-input-selector="{{ $nextInputSelector }}"
	data-prefill-by-name="{{ $prefillByName }}"
	data-prefill-by-id="{{ $prefillById }}">
	<label for="shopping_location_id">{{ $__t($label) }}
		@if(!empty($hint))
		<i class="fa-solid fa-question-circle text-muted"
			data-toggle="tooltip"
			data-trigger="hover click"
			title="{{ $hint }}"></i>
		@endif
	</label>
	<select class="form-control shopping-location-combobox"
		id="shopping_location_id"
		name="shopping_location_id"
		@if($isRequired)
		required
		@endif>
		<option value=""></option>
		@foreach($shoppinglocations as $shoppinglocation)
		<option value="{{ $shoppinglocation->id }}">{{ $shoppinglocation->name }}</option>
		@endforeach
	</select>
	<div class="invalid-feedback">{{ $__t('You have to select a store') }}</div>
</div>
