@extends('layout.default')

@if($mode == 'edit')
	@section('title', $__t('Edit recipe ingredient'))
@else
	@section('title', $__t('Add recipe ingredient'))
@endif

@section('viewJsName', 'recipeposform')

@section('content')
<div class="row">
	<div class="col-xs-12 col-md-6 col-xl-5 pb-3">
		<h1>@yield('title')</h1>
		<h3 class="text-muted">{{ $__t('Recipe') }} <strong>{{ $recipe->name }}</strong></h3>

		<script>
			Grocy.EditMode = '{{ $mode }}';
			Grocy.EditObjectParentId = {{ $recipe->id }};
			Grocy.EditObject = {!! json_encode($recipePos) !!};
			Grocy.QuantityUnits = {!! json_encode($quantityUnits) !!};
			Grocy.QuantityUnitConversionsResolved = {!! json_encode($quantityUnitConversionsResolved) !!};
		</script>

		@if($mode == 'edit')
			<script>Grocy.EditObjectId = {{ $recipePos->id }};</script>
		@endif

		<form id="recipe-pos-form" novalidate>

			@include('components.productpicker', array(
				'products' => $products,
				'nextInputSelector' => '#amount'
			))

			<div class="row">
				<div class="col">
					<div class="form-check form-group mb-1">
						<input type="hidden" name="only_check_single_unit_in_stock" value="0">
						<input @if($mode == 'edit' && $recipePos->only_check_single_unit_in_stock == 1) checked @endif class="form-check-input" type="checkbox" id="only_check_single_unit_in_stock" name="only_check_single_unit_in_stock" value="1">
						<label class="form-check-label" for="only_check_single_unit_in_stock">{{ $__t('Only check if a single unit is in stock (a different quantity can then be used above)') }}</label>
					</div>
				</div>
			</div>

			@php if($mode == 'edit') { $value = $recipePos->amount; } else { $value = 1; } @endphp
			@php if($mode == 'edit') { $initialQuId = $recipePos->qu_id; } else { $initialQuId = ''; } @endphp
			@include('components.productamountpicker', array(
				'value' => $value,
				'initialQuId' => $initialQuId,
				'additionalGroupCssClasses' => 'mb-0'
			))

			<div class="form-group">
				<label for="variable_amount">{{ $__t('Variable amount') }}&nbsp;&nbsp;<span class="small text-muted">{{ $__t('When this is not empty, it will be shown instead of the amount entered above while the amount there will still be used for stock fulfillment checking') }}</span></label>
				<input type="text" class="form-control" id="variable_amount" name="variable_amount" value="@if($mode == 'edit'){{ $recipePos->variable_amount }}@endif">
			</div>

			<div class="form-check mb-3">
				<input type="hidden" name="not_check_stock_fulfillment" value="0">
				<input @if($mode == 'edit' && ($recipePos->not_check_stock_fulfillment == 1 || FindObjectInArrayByPropertyValue($products, 'id', $recipePos->product_id)->not_check_stock_fulfillment_for_recipes == 1)) checked @endif class="form-check-input" type="checkbox" id="not_check_stock_fulfillment" name="not_check_stock_fulfillment" value="1">
				<label class="form-check-label" for="not_check_stock_fulfillment">{{ $__t('Disable stock fulfillment checking for this ingredient') }}</label>
			</div>

			<div class="form-group">
				<label for="ingredient_group">{{ $__t('Group') }}&nbsp;&nbsp;<span class="small text-muted">{{ $__t('This will be used as a headline to group ingredients together') }}</span></label>
				<input type="text" class="form-control" id="ingredient_group" name="ingredient_group" value="@if($mode == 'edit'){{ $recipePos->ingredient_group }}@endif">
			</div>

			<div class="form-group">
				<label for="note">{{ $__t('Note') }}</label>
				<textarea class="form-control" rows="2" id="note" name="note">@if($mode == 'edit'){{ $recipePos->note }}@endif</textarea>
			</div>

			@if(GROCY_FEATURE_FLAG_STOCK_PRICE_TRACKING)
			@php if($mode == 'edit') { $value = $recipePos->price_factor; } else { $value = 1; } @endphp
			@include('components.numberpicker', array(
				'id' => 'price_factor',
				'label' => 'Price factor',
				'min' => 0,
				'step' => 0.01,
				'value' => '',
				'hint' => $__t('The resulting price of this ingredient will be multiplied by this factor'),
				'invalidFeedback' => $__t('This cannot be lower than %s', '0'),
				'isRequired' => true,
				'value' => $value
			))
			@else
			<input type="hidden" name="price_factor" id="price" value="1">
			@endif

			<button id="save-recipe-pos-button" class="btn btn-success">{{ $__t('Save') }}</button>

		</form>
	</div>

	<div class="col-xs-12 col-md-6 col-xl-4 hide-when-embedded">
		@include('components.productcard')
	</div>
</div>
@stop
