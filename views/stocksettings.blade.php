@extends('layout.default')

@section('title', $__t('Stock settings'))

@section('content')
<div class="row">
	<div class="col">
		<h2 class="title">@yield('title')</h2>
	</div>
</div>

<hr class="my-2">

<div class="row">
	<div class="col-lg-4 col-md-8 col-12">
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

			@include('components.numberpicker', array(
			'id' => 'product_presets_default_due_days',
			'additionalAttributes' => 'data-setting-key="product_presets_default_due_days"',
			'label' => 'Default due days',
			'min' => -1,
			'additionalCssClasses' => 'user-setting-control'
			))

			@if(GROCY_FEATURE_FLAG_STOCK_PRODUCT_OPENED_TRACKING)
			<div class="form-group">
				<div class="custom-control custom-checkbox">
					<input type="checkbox"
						class="form-check-input custom-control-input user-setting-control"
						id="product_presets_treat_opened_as_out_of_stock"
						data-setting-key="product_presets_treat_opened_as_out_of_stock">
					<label class="form-check-label custom-control-label"
						for="product_presets_treat_opened_as_out_of_stock">
						{{ $__t('Treat opened as out of stock') }}
					</label>
				</div>
			</div>
			@endif

			@if(GROCY_FEATURE_FLAG_LABEL_PRINTER)
			<div class="form-group">
				<label for="product_presets_default_stock_label_type">{{ $__t('Default stock entry label') }}</label>
				<select class="custom-control custom-select user-setting-control"
					id="product_presets_default_stock_label_type"
					data-setting-key="product_presets_default_stock_label_type">
					<option value="0">{{ $__t('No label') }}</option>
					<option value="1">{{ $__t('Single label') }}</option>
					<option value="2">{{ $__t('Label per unit') }}</option>
				</select>
			</div>
			@endif
		</div>

		<h4 class="mt-5">{{ $__t('Stock overview') }}</h4>
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

		<div class="form-group">
			<div class="custom-control custom-checkbox">
				<input type="checkbox"
					class="form-check-input custom-control-input user-setting-control"
					id="stock_overview_show_all_out_of_stock_products"
					data-setting-key="stock_overview_show_all_out_of_stock_products">
				<label class="form-check-label custom-control-label"
					for="stock_overview_show_all_out_of_stock_products">
					{{ $__t('Show all out of stock products') }}
					<i class="fa-solid fa-question-circle text-muted"
						data-toggle="tooltip"
						data-trigger="hover click"
						title="{{ $__t('By default the stock overview page lists all products which are currently in stock or below their min. stock amount - when this is enabled, all (active) products are always shown') }}"></i>
				</label>
			</div>
		</div>

		<h4 class="mt-5">{{ $__t('Purchase') }}</h4>
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

		<h4 class="mt-5">{{ $__t('Consume') }}</h4>
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

		<h4 class="mt-5">{{ $__t('Common') }}</h4>

		@include('components.numberpicker', array(
		'id' => 'stock_decimal_places_amounts',
		'additionalAttributes' => 'data-setting-key="stock_decimal_places_amounts"',
		'label' => 'Decimal places allowed for amounts',
		'min' => 0,
		'max' => 10,
		'additionalCssClasses' => 'user-setting-control'
		))

		@if(GROCY_FEATURE_FLAG_STOCK_PRICE_TRACKING)

		@include('components.numberpicker', array(
		'id' => 'stock_decimal_places_prices_input',
		'additionalAttributes' => 'data-setting-key="stock_decimal_places_prices_input"',
		'label' => 'Decimal places allowed for prices (input)',
		'min' => 0,
		'max' => 10,
		'additionalCssClasses' => 'user-setting-control'
		))

		@include('components.numberpicker', array(
		'id' => 'stock_decimal_places_prices_display',
		'additionalAttributes' => 'data-setting-key="stock_decimal_places_prices_display"',
		'label' => 'Decimal places allowed for prices (display)',
		'min' => 0,
		'max' => 10,
		'additionalCssClasses' => 'user-setting-control'
		))

		<div class="form-group mt-n3">
			<div class="custom-control custom-checkbox">
				<input type="checkbox"
					class="form-check-input custom-control-input user-setting-control"
					id="stock_auto_decimal_separator_prices"
					data-setting-key="stock_auto_decimal_separator_prices">
				<label class="form-check-label custom-control-label"
					for="stock_auto_decimal_separator_prices">
					{{ $__t('Add decimal separator automatically for price inputs') }}
					<i class="fa-solid fa-question-circle text-muted"
						data-toggle="tooltip"
						data-trigger="hover click"
						title="{{ $__t('When enabled, you always have to enter the value including decimal places, the decimal separator will be automatically added based on the amount of allowed decimal places') }}"></i>
				</label>
			</div>
		</div>

		@endif

		<a href="{{ $U('/stockoverview') }}"
			class="btn btn-success">{{ $__t('OK') }}</a>
	</div>
</div>
@stop
