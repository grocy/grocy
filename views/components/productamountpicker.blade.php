@push('componentScripts')
<script src="{{ $U('/viewjs/components/productamountpicker.js', true) }}?v={{ $version }}"></script>
@endpush

@php if(empty($additionalGroupCssClasses)) { $additionalGroupCssClasses = ''; } @endphp
@php if(empty($additionalHtmlContextHelp)) { $additionalHtmlContextHelp = ''; } @endphp

<div class="form-group row mb-0 {{ $additionalGroupCssClasses }}">
	<div class="col">
		{!! $additionalHtmlContextHelp !!}

		<div class="row my-0">

			@include('components.numberpicker', array(
			'id' => 'display_amount',
			'label' => 'Amount',
			'min' => '0.' . str_repeat('0', $userSettings['stock_decimal_places_amounts'] - 1) . '1',
			'decimals' => $userSettings['stock_decimal_places_amounts'],
			'value' => $value,
			'invalidFeedback' => $__t('This cannot be negative and must be an integral number'),
			'additionalGroupCssClasses' => 'col-5 mb-1',
			'additionalCssClasses' => 'input-group-productamountpicker',
			'additionalHtmlContextHelp' => ''
			))

			<div class="form-group col-7 mb-1">
				<label for="qu_id">{{ $__t('Quantity unit') }}</label>
				<select required
					class="form-control input-group-productamountpicker"
					id="qu_id"
					name="qu_id"
					data-initial-qu-id="{{ $initialQuId }}">
					<option></option>
				</select>
				<div class="invalid-feedback">{{ $__t('A quantity unit is required') }}</div>
			</div>

			<div id="qu-conversion-info"
				class="col form-text text-info d-none"></div>
			<input type="hidden"
				id="amount"
				name="amount"
				value="">

		</div>
	</div>
</div>
