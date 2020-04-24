@extends('layout.default')

@if($mode == 'edit')
	@section('title', $__t('Edit QU conversion'))
@else
	@section('title', $__t('Create QU conversion'))
@endif

@section('viewJsName', 'quantityunitconversionform')

@section('content')
<div class="row">
	<div class="col">
		<h2 class="title">@yield('title')</h2>
		<hr>
	</div>
</div>
<div class="row">
	<div class="col-lg-6 col-xs-12">
	
		@if($product != null)
		<h3 class="text-muted">{{ $__t('Override for product') }} <strong>{{ $product->name }}</strong></h3>
		@else
		<h3 class="text-muted">{{ $__t('Default for QU') }} <strong>{{ $defaultQuUnit->name }}</strong></h3>
		@endif

		<script>Grocy.EditMode = '{{ $mode }}';</script>

		@if($mode == 'edit')
			<script>Grocy.EditObjectId = {{ $quConversion->id }};</script>
		@endif

		<form id="quconversion-form" novalidate>

			@if($product != null)
			<input type="hidden" name="product_id" value="{{ $product->id }}">
			@endif

			<div class="form-group">
				<label for="from_qu_id">{{ $__t('Quantity unit from') }}</label>
				<select required disabled class="form-control input-group-qu" id="from_qu_id" name="from_qu_id">
					<option></option>
					@foreach($quantityunits as $quantityunit)
						<option @if(($product != null && $quantityunit->id == $product->qu_id_stock) || ($defaultQuUnit != null && $quantityunit->id == $defaultQuUnit->id))) selected="selected" @endif value="{{ $quantityunit->id }}" data-plural-form="{{ $quantityunit->name_plural }}">{{ $quantityunit->name }}</option>
					@endforeach
				</select>
				<div class="invalid-feedback">{{ $__t('A quantity unit is required') }}</div>
			</div>

			<div class="form-group">
				<label for="to_qu_id">{{ $__t('Quantity unit to') }}</label>
				<select required class="form-control input-group-qu" id="to_qu_id" name="to_qu_id">
					<option></option>
					@foreach($quantityunits as $quantityunit)
						<option @if($mode == 'edit' && $quantityunit->id == $quConversion->to_qu_id) selected="selected" @endif value="{{ $quantityunit->id }}" data-plural-form="{{ $quantityunit->name_plural }}">{{ $quantityunit->name }}</option>
					@endforeach
				</select>
				<div class="invalid-feedback">{{ $__t('A quantity unit is required') }}</div>
			</div>

			@php if($mode == 'edit') { $value = $quConversion->factor; } else { $value = 1; } @endphp
			@include('components.numberpicker', array(
				'id' => 'factor',
				'label' => 'Factor',
				'min' => 0,
				'step' => 0.001,
				'value' => $value,
				'invalidFeedback' => $__t('This cannot be lower than %1$s and must be a valid number with max. %2$s decimal places', '0', '3'),
				'additionalHtmlElements' => '<p id="qu-conversion-info" class="form-text text-info d-none"></p>',
				'additionalCssClasses' => 'input-group-qu'
			))

			<div class="checkbox @if($mode == 'edit') d-none @endif">
				<label for="create_inverse">
					<input type="checkbox" id="create_inverse" name="create_inverse:skip" checked> {{ $__t('Create inverse QU conversion') }}
					<span id="qu-conversion-inverse-info" class="form-text text-info d-none"></span>
				</label>
			</div>

			@include('components.userfieldsform', array(
				'userfields' => $userfields,
				'entity' => 'quantity_unit_conversions'
			))

			<button id="save-quconversion-button" class="btn btn-success">{{ $__t('Save') }}</button>

		</form>
	</div>
</div>
@stop
