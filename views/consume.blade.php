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

			@include('components.numberpicker', array(
				'id' => 'amount',
				'label' => 'Amount',
				'hintId' => 'amount_qu_unit',
				'min' => 1,
				'value' => 1,
				'invalidFeedback' => $L('The amount cannot be lower than #1', '1')
			))

			<div class="checkbox">
				<label for="spoiled">
					<input type="checkbox" id="spoiled" name="spoiled"> {{ $L('Spoiled') }}
				</label>
			</div>

			<button id="save-consume-button" class="btn btn-success">{{ $L('OK') }}</button>

		</form>
	</div>

	<div class="col-xs-12 col-md-6 col-xl-4">
		@include('components.productcard')
	</div>
</div>
@stop
