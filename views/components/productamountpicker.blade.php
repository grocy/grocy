@push('componentScripts')
	<script src="{{ $U('/viewjs/components/productamountpicker.js', true) }}?v={{ $version }}"></script>
@endpush

@php if(empty($additionalGroupCssClasses)) { $additionalGroupCssClasses = ''; } @endphp

<div class="form-group row {{ $additionalGroupCssClasses }}">
	<div class="col">
		<div class="row">

			@include('components.numberpicker', array(
				'id' => 'display_amount',
				'label' => 'Amount',
				'min' => 0,
				'value' => $value,
				'invalidFeedback' => $__t('This cannot be negative and must be an integral number'),
				'additionalGroupCssClasses' => 'col-4 mb-1',
				'additionalCssClasses' => 'input-group-productamountpicker'
			))

			<div class="form-group col-8 mb-1">
				<label for="qu_id">{{ $__t('Quantity unit') }}</label>
				<select required class="form-control input-group-productamountpicker" id="qu_id" name="qu_id" data-initial-qu-id="{{ $initialQuId }}">
					<option></option>
				</select>
				<div class="invalid-feedback">{{ $__t('A quantity unit is required') }}</div>
			</div>

			<div id="qu-conversion-info" class="col form-text text-info d-none"></div>
			<input type="hidden" id="amount" name="amount" value="">

		</div>
	</div>
</div>
