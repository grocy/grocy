@extends('layout.default')

@if($mode == 'edit')
@section('title', $__t('Edit recipe ingredient'))
@else
@section('title', $__t('Add recipe ingredient'))
@endif

@section('content')
<script>
	Grocy.DefaultMinAmount = '{{$DEFAULT_MIN_AMOUNT}}';
</script>

<div class="row">
	<div class="col">
		<div class="title-related-links">
			<h2 class="title">
				@yield('title')<br>
				<span class="text-muted small">{{ $__t('Recipe') }} <strong>{{ $recipe->name }}</strong></span>
			</h2>
		</div>
	</div>
</div>

<hr class="my-2">

<div class="row">
	<div class="col-12 col-md-6 col-xl-5 pb-3">
		<script>
			Grocy.EditMode = '{{ $mode }}';
			Grocy.EditObjectParentId = {{ $recipe->id }};
			Grocy.EditObject = {!! json_encode($recipePos) !!};
			Grocy.QuantityUnits = {!! json_encode($quantityUnits) !!};
			Grocy.QuantityUnitConversionsResolved = {!! json_encode($quantityUnitConversionsResolved) !!};
		</script>

		@if($mode == 'edit')
		<script>
			Grocy.EditObjectId = {{ $recipePos->id }};
		</script>
		@endif

		<form id="recipe-pos-form"
			novalidate>

			@include('components.productpicker', array(
			'products' => $products,
			'barcodes' => $barcodes,
			'nextInputSelector' => '#amount'
			))

			<div class="form-group mb-2 @if(!GROCY_FEATURE_FLAG_STOCK) d-none @endif">
				<div class="custom-control custom-checkbox">
					<input @if($mode=='edit'
						&&
						$recipePos->only_check_single_unit_in_stock == 1) checked @endif class="form-check-input custom-control-input" type="checkbox" id="only_check_single_unit_in_stock" name="only_check_single_unit_in_stock" value="1">
					<label class="form-check-label custom-control-label"
						for="only_check_single_unit_in_stock">{{ $__t('Only check if any amount is in stock') }}&nbsp;<i class="fa-solid fa-question-circle text-muted"
							data-toggle="tooltip"
							data-trigger="hover click"
							title="{{ $__t('A different amount/unit can then be used below while for stock fulfillment checking it is sufficient when any amount of the product in stock') }}"></i></label>
				</div>
			</div>

			@php if($mode == 'edit') { $value = $recipePos->amount; } else { $value = 1; } @endphp
			@php if($mode == 'edit') { $initialQuId = $recipePos->qu_id; } else { $initialQuId = ''; } @endphp
			@include('components.productamountpicker', array(
			'value' => $value,
			'initialQuId' => $initialQuId,
			'additionalGroupCssClasses' => 'mb-2'
			))

			<div class="form-group">
				<label for="variable_amount">{{ $__t('Variable amount') }}&nbsp;<i class="fa-solid fa-question-circle text-muted"
						data-toggle="tooltip"
						data-trigger="hover click"
						title="{{ $__t('When this is not empty, it will be shown instead of the amount entered above while the amount there will still be used for stock fulfillment checking') }}"></i></label>
				<input type="text"
					class="form-control"
					id="variable_amount"
					name="variable_amount"
					value="@if($mode == 'edit'){{ $recipePos->variable_amount }}@endif">

				<div class="custom-control custom-checkbox">
					<input @if($mode=='edit'
						&&
						$recipePos->round_up == 1) checked @endif class="form-check-input custom-control-input" type="checkbox" id="round_up" name="round_up" value="1">
					<label class="form-check-label custom-control-label"
						for="round_up">{{ $__t('Round up quantity amounts to the nearest whole number') }}</label>
				</div>
			</div>

			<div class="form-group @if(!GROCY_FEATURE_FLAG_STOCK) d-none @endif">
				<div class="custom-control custom-checkbox">
					<input @if($mode=='edit'
						&&
						($recipePos->not_check_stock_fulfillment == 1 || FindObjectInArrayByPropertyValue($products, 'id', $recipePos->product_id)->not_check_stock_fulfillment_for_recipes == 1)) checked @endif class="form-check-input custom-control-input" type="checkbox" id="not_check_stock_fulfillment" name="not_check_stock_fulfillment" value="1">
					<label class="form-check-label custom-control-label"
						for="not_check_stock_fulfillment">{{ $__t('Disable stock fulfillment checking for this ingredient') }}</label>
				</div>
			</div>

			<div class="form-group">
				<label for="ingredient_group">{{ $__t('Group') }}&nbsp;<i class="fa-solid fa-question-circle text-muted"
						data-toggle="tooltip"
						data-trigger="hover click"
						title="{{ $__t('This will be used as a headline to group ingredients together') }}"></i></label>
				<input type="text"
					class="form-control"
					id="ingredient_group"
					name="ingredient_group"
					value="@if($mode == 'edit'){{ $recipePos->ingredient_group }}@endif">
			</div>

			<div class="form-group">
				<label for="note">{{ $__t('Note') }}</label>
				<textarea class="form-control"
					rows="2"
					id="note"
					name="note">@if($mode == 'edit'){{ $recipePos->note }}@endif</textarea>
			</div>

			@if(GROCY_FEATURE_FLAG_STOCK_PRICE_TRACKING)
			@php if($mode == 'edit') { $value = $recipePos->price_factor; } else { $value = 1; } @endphp
			@include('components.numberpicker', array(
			'id' => 'price_factor',
			'label' => 'Price factor',
			'min' => $DEFAULT_MIN_AMOUNT,
			'decimals' => $userSettings['stock_decimal_places_amounts'],
			'value' => '',
			'hint' => $__t('The resulting price of this ingredient will be multiplied by this factor'),
			'isRequired' => true,
			'value' => $value,
			'additionalCssClasses' => 'locale-number-input locale-number-quantity-amount'
			))
			@else
			<input type="hidden"
				name="price_factor"
				id="price"
				value="1">
			@endif

			<button id="save-recipe-pos-button"
				class="btn btn-success">{{ $__t('Save') }}</button>

		</form>
	</div>
</div>
@stop
