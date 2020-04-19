@extends('layout.default')

@if($mode == 'edit')
	@section('title', $__t('Edit shopping list item'))
@else
	@section('title', $__t('Create shopping list item'))
@endif

@section('viewJsName', 'shoppinglistitemform')

@section('content')
<div class="row">
	<div class="col">
		<h2 class="title">@yield('title')</h2>
		<hr>
	</div>
</div>
<div class="row">
	<div class="col-xs-12 col-md-6 col-xl-4 pb-3">
		<script>Grocy.EditMode = '{{ $mode }}';</script>

		@if($mode == 'edit')
			<script>Grocy.EditObjectId = {{ $listItem->id }};</script>
		@endif

		<form id="shoppinglist-form" novalidate>

			@if(GROCY_FEATURE_FLAG_SHOPPINGLIST_MULTIPLE_LISTS)
			<div class="form-group">
				<label for="shopping_list_id">{{ $__t('Shopping list') }}</label>
				<select class="form-control" id="shopping_list_id" name="shopping_list_id">
					@foreach($shoppingLists as $shoppingList)
						<option @if($mode == 'edit' && $shoppingList->id == $listItem->shopping_list_id) selected="selected" @endif value="{{ $shoppingList->id }}">{{ $shoppingList->name }}</option>
					@endforeach
				</select>
			</div>
			@else
			<input type="hidden" id="shopping_list_id" name="shopping_list_id" value="1">
			@endif

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
				'invalidFeedback' => $__t('The amount cannot be lower than %s', '1')
			))

			<div class="form-group">
				<label for="note">{{ $__t('Note') }}</label>
				<textarea class="form-control" rows="2" id="note" name="note">@if($mode == 'edit'){{ $listItem->note }}@endif</textarea>
			</div>

			<button id="save-shoppinglist-button" class="btn btn-success">{{ $__t('Save') }}</button>

		</form>
	</div>

	<div class="col-xs-12 col-md-6 col-xl-4 hide-when-embedded">
		@include('components.productcard')
	</div>
</div>
@stop
