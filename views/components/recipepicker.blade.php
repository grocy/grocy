@once
@push('componentScripts')
<script src="{{ $U('/viewjs/components/recipepicker.js', true) }}?v={{ $version }}"></script>
@endpush
@endonce

@php if(empty($prefillByName)) { $prefillByName = ''; } @endphp
@php if(empty($prefillById)) { $prefillById = ''; } @endphp
@php if(!isset($isRequired)) { $isRequired = true; } @endphp
@php if(empty($hint)) { $hint = ''; } @endphp
@php if(empty($nextInputSelector)) { $nextInputSelector = ''; } @endphp

<div class="form-group"
	data-next-input-selector="{{ $nextInputSelector }}"
	data-prefill-by-name="{{ $prefillByName }}"
	data-prefill-by-id="{{ $prefillById }}">
	<label class="w-100"
		for="recipe_id">{{ $__t('Recipe') }}
		@if(!empty($hint))
		<i class="fas fa-question-circle text-muted"
			data-toggle="tooltip"
			data-trigger="hover click"
			title="{{ $hint }}"></i>
		@endif
		<i class="fas fa-barcode float-right mt-1"></i>
	</label>
	<select class="form-control recipe-combobox"
		id="recipe_id"
		name="recipe_id"
		@if($isRequired)
		required
		@endif>
		<option value=""></option>
		@foreach($recipes as $recipe)
		<option value="{{ $recipe->id }}">{{ $recipe->name }}</option>
		@endforeach
	</select>
	<div class="invalid-feedback">{{ $__t('You have to select a recipe') }}</div>
</div>
