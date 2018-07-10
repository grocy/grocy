@extends('layout.default')

@section('title', $L('Purchase'))
@section('activeNav', 'purchase')
@section('viewJsName', 'purchase')

@section('content')
<div class="row">
	<div class="col-lg-4 col-xs-12">
		<h1>@yield('title')</h1>

		<form id="purchase-form">

			<div class="form-group">
				<label for="product_id">{{ $L('Product') }}&nbsp;&nbsp;<i class="fa fa-barcode"></i><span id="barcode-lookup-disabled-hint" class="small text-muted d-none">&nbsp;&nbsp;Barcode lookup is disabled</span></label>
				<select class="form-control combobox" id="product_id" name="product_id" required>
					<option value=""></option>
					@foreach($products as $product)
						<option data-additional-searchdata="{{ $product->barcode }}" value="{{ $product->id }}">{{ $product->name }}</option>
					@endforeach
				</select>
				<div class="invalid-feedback"></div>
				<div id="flow-info-addbarcodetoselection" class="text-muted small d-none"><strong><span id="addbarcodetoselection"></span></strong> {{ $L('will be added to the list of barcodes for the selected product on submit') }}</div>
			</div>

			@include('components.datepicker', array(
				'id' => 'best_before_date',
				'label' => 'Best before'
			))

			<div class="form-group">
				<label for="amount">{{ $L('Amount') }}&nbsp;&nbsp;<span id="amount_qu_unit" class="small text-muted"></span></label>
				<input type="number" class="form-control" id="amount" name="amount" value="1" min="1" required>
				<div class="invalid-feedback"></div>
			</div>

			<button id="save-purchase-button" type="submit" class="btn btn-success">{{ $L('OK') }}</button>

		</form>
	</div>

	<div class="col-lg-6 col-xs-12">
		@include('components.productcard')
	</div>
</div>
@stop
