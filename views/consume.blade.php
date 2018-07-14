@extends('layout.default')

@section('title', $L('Consume'))
@section('activeNav', 'consume')
@section('viewJsName', 'consume')

@section('content')
<div class="row">
	<div class="col-xs-12 col-md-6 col-xl-4 pb-3">
		<h1>@yield('title')</h1>

		<form id="consume-form" novalidate>

			@include('components.productpicker', array(
				'products' => $products,
				'nextInputSelector' => '#amount',
				'disallowAddProductWorkflows' => true
			))

			<div class="form-group">
				<label for="amount">{{ $L('Amount') }}&nbsp;&nbsp;<span id="amount_qu_unit" class="small text-muted"></span></label>
				<input type="number" class="form-control" id="amount" name="amount" value="1" min="1" required>
				<div class="invalid-feedback">{{ $L('The amount cannot be lower than #1', '0') }}</div>
			</div>

			<div class="checkbox">
				<label for="spoiled">
					<input type="checkbox" id="spoiled" name="spoiled"> {{ $L('Spoiled') }}
				</label>
			</div>

			<button id="save-consume-button" type="submit" class="btn btn-success">{{ $L('OK') }}</button>

		</form>
	</div>

	<div class="col-xs-12 col-md-6 col-xl-4">
		@include('components.productcard')
	</div>
</div>
@stop
