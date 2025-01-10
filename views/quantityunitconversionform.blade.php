@extends('layout.default')

@if($mode == 'edit')
@section('title', $__t('Edit QU conversion'))
@else
@section('title', $__t('Create QU conversion'))
@endif

@section('content')
<div class="row">
	<div class="col">
		<div class="title-related-links">
			<h2 class="title">
				@yield('title')<br>
				@if($product != null)
				<span class="text-muted small">{{ $__t('Override for product') }} <strong>{{ $product->name }}</strong></span>
				@else
				<span class="text-muted small">{{ $__t('Default for QU') }} <strong>{{ $defaultQuUnit->name }}</strong></span>
				@endif
			</h2>
		</div>
	</div>
</div>

<hr class="my-2">

<div class="row">
	<div class="col-lg-6 col-12">

		<script>
			Grocy.EditMode = '{{ $mode }}';
		</script>

		@if($mode == 'edit')
		<script>
			Grocy.EditObjectId = {{ $quConversion->id }};
		</script>
		@endif

		<form id="quconversion-form"
			novalidate>

			@if($product != null)
			<input type="hidden"
				name="product_id"
				value="{{ $product->id }}">
			@endif

			<div class="form-group">
				<label for="from_qu_id">{{ $__t('Quantity unit from') }}</label>
				<select required
					class="custom-control custom-select input-group-qu"
					id="from_qu_id"
					name="from_qu_id">
					<option></option>
					@foreach($quantityunits as $quantityunit)
					@php
					$selected = false;
					if ($mode == 'edit')
					{
					if ($quantityunit->id == $quConversion->from_qu_id)
					{
					$selected = true;
					}
					}
					else
					{
					if ($product != null && $quantityunit->id == $product->qu_id_stock)
					{
					$selected = true;
					}
					else
					{
					if ($quantityunit->id == $defaultQuUnit->id)
					{
					$selected = true;
					}
					}
					}
					@endphp
					<option @if($selected)
						selected="selected"
						@endif
						value="{{ $quantityunit->id }}"
						data-plural-form="{{ $quantityunit->name_plural }}">{{ $quantityunit->name }}</option>
					@endforeach
				</select>
				<div class="invalid-feedback">{{ $__t('A quantity unit is required') }}</div>
			</div>

			<div class="form-group">
				<label for="to_qu_id">{{ $__t('Quantity unit to') }}</label>
				<select required
					class="custom-control custom-select input-group-qu"
					id="to_qu_id"
					name="to_qu_id">
					<option></option>
					@foreach($quantityunits as $quantityunit)
					<option @if($mode=='edit'
						&&
						$quantityunit->id == $quConversion->to_qu_id) selected="selected" @endif value="{{ $quantityunit->id }}" data-plural-form="{{ $quantityunit->name_plural }}">{{ $quantityunit->name }}</option>
					@endforeach
				</select>
				<div class="invalid-feedback">{{ $__t('A quantity unit is required') }}</div>
			</div>

			@php if($mode == 'edit') { $value = $quConversion->factor; } else { $value = 1; } @endphp
			@include('components.numberpicker', array(
			'id' => 'factor',
			'label' => 'Factor',
			'min' => $DEFAULT_MIN_AMOUNT,
			'decimals' => $userSettings['stock_decimal_places_amounts'],
			'value' => $value,
			'additionalHtmlElements' => '<p id="qu-conversion-info"
				class="form-text text-info d-none mb-0"></p>
			<p id="qu-conversion-inverse-info"
				class="form-text text-info d-none"></p>',
			'additionalCssClasses' => 'input-group-qu locale-number-input locale-number-quantity-amount'
			))

			@include('components.userfieldsform', array(
			'userfields' => $userfields,
			'entity' => 'quantity_unit_conversions'
			))

			<button id="save-quconversion-button"
				class="btn btn-success">{{ $__t('Save') }}</button>

		</form>
	</div>
</div>
@stop
