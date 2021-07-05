@once
@push('componentScripts')
<script src="{{ $U('/viewjs/components/productamountpicker.js', true) }}?v={{ $version }}"></script>
@endpush
@endonce

@php if(empty($additionalGroupCssClasses)) { $additionalGroupCssClasses = ''; } @endphp
@php if(empty($additionalHtmlContextHelp)) { $additionalHtmlContextHelp = ''; } @endphp
@php if(empty($additionalHtmlElements)) { $additionalHtmlElements = ''; } @endphp
@php if(empty($label)) { $label = 'Amount'; } @endphp
@php if(empty($initialQuId)) { $initialQuId = '-1'; } @endphp
@php if(!isset($isRequired)) { $isRequired = true; } @endphp

<div class="form-group row {{ $additionalGroupCssClasses }}">
	<div class="col">
		{!! $additionalHtmlContextHelp !!}

		<div class="row">

			@include('components.numberpicker', array(
			'id' => 'display_amount',
			'label' => $label,
			'min' => $DEFAULT_MIN_AMOUNT,
			'decimals' => $userSettings['stock_decimal_places_amounts'],
			'value' => $value,
			'additionalGroupCssClasses' => 'col-sm-5 col-12 my-0',
			'additionalCssClasses' => 'input-group-productamountpicker locale-number-input locale-number-quantity-amount',
			'additionalHtmlContextHelp' => '',
			'additionalHtmlElements' => ''
			))

			<div class="col-sm-7 col-12">
				<label for="qu_id">{{ $__t('Quantity unit') }}</label>
				<select @if($isRequired)
					required
					@endif
					class="custom-control custom-select input-group-productamountpicker"
					id="qu_id"
					name="qu_id"
					data-initial-qu-id="{{ $initialQuId }}">
					<option></option>
				</select>
				<div class="invalid-feedback">{{ $__t('A quantity unit is required') }}</div>
			</div>

			<div id="qu-conversion-info"
				class="ml-3 my-0 form-text text-info d-none w-100"></div>

			{!! $additionalHtmlElements !!}

			<input type="hidden"
				id="amount"
				name="amount"
				value="">

		</div>
	</div>
</div>
