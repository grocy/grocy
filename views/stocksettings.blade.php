@extends($rootLayout)

@section('title', $__t('Stock settings'))

@section('viewJsName', 'stocksettings')

@section('content')
<div class="row">
	<div class="col">
		<h2 class="title">@yield('title')</h2>
	</div>
</div>

<hr class="my-2">

<div class="row">
	<div class="col-lg-6 col-12">
		<div id="productpresets">
			<h4>{{ $__t('Presets for new products') }}</h4>

			@if(GROCY_FEATURE_FLAG_STOCK_LOCATION_TRACKING)
			<div class="form-group">
				<label for="product_presets_location_id">{{ $__t('Location') }}</label>
				<select class="custom-control custom-select user-setting-control"
					id="product_presets_location_id"
					data-setting-key="product_presets_location_id">
					<option value="-1"></option>
					@foreach($locations as $location)
					<option value="{{ $location->id }}">{{ $location->name }}</option>
					@endforeach
				</select>
			</div>
			@endif

			<div class="form-group">
				<label for="product_presets_product_group_id">{{ $__t('Product group') }}</label>
				<select class="custom-control custom-select user-setting-control"
					id="product_presets_product_group_id"
					data-setting-key="product_presets_product_group_id">
					<option value="-1"></option>
					@foreach($productGroups as $productGroup)
					<option value="{{ $productGroup->id }}">{{ $productGroup->name }}</option>
					@endforeach
				</select>
			</div>

			<div class="form-group">
				<label for="product_presets_qu_id">{{ $__t('Quantity unit') }}</label>
				<select class="custom-control custom-select user-setting-control"
					id="product_presets_qu_id"
					data-setting-key="product_presets_qu_id">
					<option value="-1"></option>
					@foreach($quantityunits as $quantityunit)
					<option value="{{ $quantityunit->id }}">{{ $quantityunit->name }}</option>
					@endforeach
				</select>
			</div>
		</div>

		<h4 class="mt-2">{{ $__t('Stock overview') }}</h4>
		@include('components.numberpicker', array(
		'id' => 'stock_due_soon_days',
		'additionalAttributes' => 'data-setting-key="stock_due_soon_days"',
		'label' => 'Due soon days',
		'min' => 1,
		'additionalCssClasses' => 'user-setting-control'
		))

		<div class="form-group">
			<div class="custom-control custom-checkbox">
				<input type="checkbox"
					class="form-check-input custom-control-input user-setting-control"
					id="show_icon_on_stock_overview_page_when_product_is_on_shopping_list"
					data-setting-key="show_icon_on_stock_overview_page_when_product_is_on_shopping_list">
				<label class="form-check-label custom-control-label"
					for="show_icon_on_stock_overview_page_when_product_is_on_shopping_list">
					{{ $__t('Show an icon if the product is already on the shopping list') }}
				</label>
			</div>
		</div>

		<h4 class="mt-2">{{ $__t('Purchase') }}</h4>
		@include('components.numberpicker', array(
		'id' => 'stock_default_purchase_amount',
		'additionalAttributes' => 'data-setting-key="stock_default_purchase_amount"',
		'label' => 'Default amount for purchase',
		'min' => '0.',
		'decimals' => $userSettings['stock_decimal_places_amounts'],
		'additionalCssClasses' => 'user-setting-control locale-number-input locale-number-quantity-amount',
		))

		<div class="form-group">
			<div class="custom-control custom-checkbox">
				<input type="checkbox"
					class="form-check-input custom-control-input user-setting-control"
					id="show_purchased_date_on_purchase"
					data-setting-key="show_purchased_date_on_purchase">
				<label class="form-check-label custom-control-label"
					for="show_purchased_date_on_purchase">
					{{ $__t('Show purchased date on purchase and inventory page (otherwise the purchased date defaults to today)') }}
				</label>
			</div>
		</div>

		<div class="form-group">
			<div class="custom-control custom-checkbox">
				<input type="checkbox"
					class="form-check-input custom-control-input user-setting-control"
					id="show_warning_on_purchase_when_due_date_is_earlier_than_next"
					data-setting-key="show_warning_on_purchase_when_due_date_is_earlier_than_next">
				<label class="form-check-label custom-control-label"
					for="show_warning_on_purchase_when_due_date_is_earlier_than_next">
					{{ $__t('Show a warning when the due date of the purchased product is earlier than the next due date in stock') }}
				</label>
			</div>
		</div>

		<h4 class="mt-2">{{ $__t('Consume') }}</h4>
		@include('components.numberpicker', array(
		'id' => 'stock_default_consume_amount',
		'additionalAttributes' => 'data-setting-key="stock_default_consume_amount"',
		'label' => 'Default amount for consume',
		'min' => 0,
		'decimals' => $userSettings['stock_decimal_places_amounts'],
		'additionalCssClasses' => 'user-setting-control locale-number-input locale-number-quantity-amount',
		'additionalGroupCssClasses' => 'mb-0'
		))

		<div class="form-group">
			<div class="custom-control custom-checkbox">
				<input type="checkbox"
					class="form-check-input custom-control-input user-setting-control"
					id="stock_default_consume_amount_use_quick_consume_amount"
					data-setting-key="stock_default_consume_amount_use_quick_consume_amount">
				<label class="form-check-label custom-control-label"
					for="stock_default_consume_amount_use_quick_consume_amount">
					{{ $__t('Use the products "Quick consume amount"') }}
				</label>
			</div>
		</div>

		<h4 class="mt-2">{{ $__t('Common') }}</h4>

		@include('components.numberpicker', array(
		'id' => 'stock_decimal_places_amounts',
		'additionalAttributes' => 'data-setting-key="stock_decimal_places_amounts"',
		'label' => 'Decimal places allowed for amounts',
		'min' => 0,
		'additionalCssClasses' => 'user-setting-control'
		))

		@if(GROCY_FEATURE_FLAG_STOCK_PRICE_TRACKING)
		@include('components.numberpicker', array(
		'id' => 'stock_decimal_places_prices',
		'additionalAttributes' => 'data-setting-key="stock_decimal_places_prices"',
		'label' => 'Decimal places allowed for prices',
		'min' => 0,
		'additionalCssClasses' => 'user-setting-control'
		))
		@endif

		<a href="{{ $U('/stockoverview') }}"
			class="btn btn-success">{{ $__t('OK') }}</a>
	</div>
</div>
@stop
