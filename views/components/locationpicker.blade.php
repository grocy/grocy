@push('componentScripts')
<script src="{{ $U('/viewjs/components/locationpicker.js', true) }}?v={{ $version }}"></script>
@endpush

@php if(empty($prefillByName)) { $prefillByName = ''; } @endphp
@php if(empty($prefillById)) { $prefillById = ''; } @endphp
@php if(!isset($isRequired)) { $isRequired = true; } @endphp
@php if(empty($hint)) { $hint = ''; } @endphp
@php if(empty($nextInputSelector)) { $nextInputSelector = ''; } @endphp

<div class="form-group"
	data-next-input-selector="{{ $nextInputSelector }}"
	data-prefill-by-name="{{ $prefillByName }}"
	data-prefill-by-id="{{ $prefillById }}">
	<label for="location_id">{{ $__t('Location') }}&nbsp;&nbsp;<span @if(!empty($hintId))id="{{ $hintId }}" @endif
			class="small text-muted">{{ $hint }}</span></label>
	<select class="form-control location-combobox"
		id="location_id"
		name="location_id"
		@if($isRequired)
		required
		@endif>
		<option value=""></option>
		@foreach($locations as $location)
		<option value="{{ $location->id }}">{{ $location->name }}</option>
		@endforeach
	</select>
	<div class="invalid-feedback">{{ $__t('You have to select a location') }}</div>
</div>
