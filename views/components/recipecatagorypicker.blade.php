@push('componentScripts')
	<script src="{{ $U('/viewjs/components/recipecatagorypicker.js', true) }}?v={{ $version }}"></script>
@endpush

@php if(empty($prefillByName)) { $prefillByName = ''; } @endphp
@php if(empty($prefillById)) { $prefillById = ''; } @endphp
@php if(!isset($isRequired)) { $isRequired = true; } @endphp
@php if(empty($hint)) { $hint = ''; } @endphp
@php if(empty($hintId)) { $hintId = ''; } @endphp
@php if(empty($nextInputSelector)) { $nextInputSelector = ''; } @endphp

<div class="form-group" data-next-input-selector="{{ $nextInputSelector }}" data-prefill-by-name="{{ $prefillByName }}" data-prefill-by-id="{{ $prefillById }}">
	<label for="recipe_catagory_id">{{ $__t('Recipe Catagory') }}&nbsp;&nbsp;<span id="{{ $hintId }}" class="small text-muted">{{ $hint }}</span></label>
	<select class="form-control recipe-catagory-combobox" id="recipe_catagory_id" name="recipe_catagory_id" @if($isRequired) required @endif>
		<option value=""></option>
		@foreach($recipeCatagories as $recipeCatagory)
			<option value="{{ $recipeCatagory->id }}">{{ $recipeCatagory->name }}</option>
		@endforeach
	</select>
	<div class="invalid-feedback">{{ $__t('You have to select a recipe catagory') }}</div>
</div>
