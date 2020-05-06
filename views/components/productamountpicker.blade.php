@push('componentScripts')
	<script src="{{ $U('/viewjs/components/productamountpicker.js', true) }}?v={{ $version }}"></script>
@endpush

@php if(empty($additionalGroupCssClasses)) { $additionalGroupCssClasses = ''; } @endphp
@php if(!isset($value)) { $value = 1; } @endphp
@php if(empty($id)) { $id = 'display_amount'; } @endphp
@php if(empty($label)) { $label = 'Amount'; } @endphp
@php if(empty($min)) { $min = 0; } @endphp
@php if(empty($max)) { $max = 999999; } @endphp
@php if(empty($step)) { $step = 1; } @endphp
@php if(empty($hint)) { $hint = ''; } @endphp
@php if(empty($hintId)) { $hintId = ''; } @endphp
@php if(empty($additionalCssClasses)) { $additionalCssClasses = ''; } @endphp
@php if(empty($additionalGroupCssClasses)) { $additionalGroupCssClasses = ''; } @endphp
@php if(empty($additionalAttributes)) { $additionalAttributes = ''; } @endphp
@php if(empty($additionalHtmlElements)) { $additionalHtmlElements = ''; } @endphp
@php if(empty($additionalHtmlContextHelp)) { $additionalHtmlContextHelp = ''; } @endphp
@php if(!isset($isRequired)) { $isRequired = true; } @endphp
@php if(!isset($noNameAttribute)) { $noNameAttribute = false; } @endphp

<div class="form-group">
			@include('components.numberpicker', array(
				'id' => $id,
				'label' => $label,
				'min' => $min,
				'value' => $value,
				'hintId' => $hintId,
				'invalidFeedback' => $__t('This cannot be negative and must be an integral number'),
				'additionalHtmlContextHelp' => $additionalHtmlContextHelp,
				'additionalCssClasses' => 'input-group-productamountpicker'
			))

			<div id="group-qu-id" class="form-group d-none">
				<label for="qu-id">{{ $__t('Quantity unit') }}</label>
				<select required class="form-control input-group-productamountpicker" id="qu-id" name="qu-id" data-initial-qu-id="{{ $initialQuId }}">
					<option></option>
				</select>
				<div class="invalid-feedback">{{ $__t('A quantity unit is required') }}</div>
			</div>

			@include('components.numberpicker', array(
				'id' => 'qu-factor-purchase-to-stock',
				'label' => 'Factor purchase to stock quantity unit',
				'min' => 1,
				'additionalGroupCssClasses' => 'd-none',
				'invalidFeedback' => $__t('The amount cannot be lower than %s', '1'),
				'additionalCssClasses' => 'input-group-qu'
			))

			<div id="qu-conversion-info" class="col form-text text-info d-none"></div>
			<input type="hidden" id="amount" name="amount" value="">

</div>
