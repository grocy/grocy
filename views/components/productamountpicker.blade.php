@push('componentScripts')
<script src="{{ $U('/viewjs/components/productamountpicker.js', true) }}?v={{ $version }}"></script>
@endpush

@php if(empty($additionalGroupCssClasses)) { $additionalGroupCssClasses = ''; } @endphp
@php if(empty($additionalHtmlContextHelp)) { $additionalHtmlContextHelp = ''; } @endphp
@php if(empty($additionalHtmlElements)) { $additionalHtmlElements = ''; } @endphp
@php if(empty($label)) { $label = 'Amount'; } @endphp

<div class="form-group row {{ $additionalGroupCssClasses }}">
	<div class="col">
		{!! $additionalHtmlContextHelp !!}

		<div class="row">

			@include('components.numberpicker', array(
			'id' => 'display_amount',
			'label' => $label,
			'min' => '0.' . str_repeat('0', $userSettings['stock_decimal_places_amounts'] - 1) . '1',
			'decimals' => $userSettings['stock_decimal_places_amounts'],
			'value' => $value,
			'invalidFeedback' => $__t('This cannot be negative and must be an integral number'),
			'additionalGroupCssClasses' => 'col-sm-5 col-xs-12 my-0',
			'additionalCssClasses' => 'input-group-productamountpicker',
			'additionalHtmlContextHelp' => '',
			'additionalHtmlElements' => ''
			))

			<div class="col-sm-7 col-xs-12">
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
				class="ml-3 my-0 form-text text-info d-none w-100"></div>

			{!! $additionalHtmlElements !!}

			<input type="hidden"
				id="amount"
				name="amount"
				value="">

		</div>
	</div>
</div>
