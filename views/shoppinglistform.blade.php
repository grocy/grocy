@extends('layout.default')

@if($mode == 'edit')
	@section('title', $L('Edit shopping list item'))
@else
	@section('title', $L('Create shopping list item'))
@endif

@section('viewJsName', 'shoppinglistform')

@section('content')
<div class="row">
	<div class="col-xs-12 col-md-6 col-xl-4 pb-3">
		<h1>@yield('title')</h1>

		<script>Grocy.EditMode = '{{ $mode }}';</script>

		@if($mode == 'edit')
			<script>Grocy.EditObjectId = {{ $listItem->id }};</script>
		@endif

		<form id="shoppinglist-form" novalidate>

			@php if($mode == 'edit') { $productId = $listItem->product_id; } else { $productId = ''; } @endphp
			@include('components.productpicker', array(
				'products' => $products,
				'nextInputSelector' => '#amount',
				'isRequired' => false,
				'prefillById' => $productId
			))

			@php if($mode == 'edit') { $value = $listItem->amount; } else { $value = 1; } @endphp
			@include('components.numberpicker', array(
				'id' => 'amount',
				'label' => 'Amount',
				'hintId' => 'amount_qu_unit',
				'min' => 0,
				'value' => $value,
				'invalidFeedback' => $L('The amount cannot be lower than #1', '1')
			))

			<div class="form-group">
				<label for="note">{{ $L('Note') }}</label>
				<textarea class="form-control" rows="2" id="note" name="note">@if($mode == 'edit'){{ $listItem->note }}@endif</textarea>
			</div>

			<button id="save-shoppinglist-button" class="btn btn-success">{{ $L('Save') }}</button>

		</form>
	</div>

	<div class="col-xs-12 col-md-6 col-xl-4">
		@include('components.productcard')
	</div>
</div>
@stop
