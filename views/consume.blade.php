@extends('layout.default')

@section('title', $L('Consume'))
@section('activeNav', 'consume')
@section('viewJsName', 'consume')

@section('content')
<div class="row">
	<div class="col-lg-4 col-xs-12">
		<h1>@yield('title')</h1>

		<form id="consume-form">

			<div class="form-group">
				<label for="product_id">{{ $L('Product') }}&nbsp;&nbsp;<i class="fa fa-barcode"></i></label>
				<select class="form-control combobox" id="product_id" name="product_id" required>
					<option value=""></option>
					@foreach($products as $product)
						<option data-additional-searchdata="{{ $product->barcode }}" value="{{ $product->id }}">{{ $product->name }}</option>
					@endforeach
				</select>
				<div id="product-error" class="invalid-feedback"></div>
			</div>

			<div class="form-group">
				<label for="amount">{{ $L('Amount') }}&nbsp;&nbsp;<span id="amount_qu_unit" class="small text-muted"></span></label>
				<input type="number" class="form-control" id="amount" name="amount" value="1" min="1" required>
				<div class="invalid-feedback"></div>
			</div>

			<div class="checkbox">
				<label for="spoiled">
					<input type="checkbox" id="spoiled" name="spoiled"> {{ $L('Spoiled') }}
				</label>
			</div>

			<button id="save-consume-button" type="submit" class="btn btn-success">{{ $L('OK') }}</button>

		</form>
	</div>

	<div class="col-lg-6 col-xs-12">
		@include('components.productcard')
	</div>
</div>
@stop
