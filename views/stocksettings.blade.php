@extends('layout.default')

@section('title', $__t('Stock settings'))

@section('viewJsName', 'stocksettings')

@section('content')
<div class="row">
	<div class="col">
		<h2 class="title">@yield('title')</h2>
		<hr>
	</div>
</div>
<div class="row">
	<div class="col-lg-6 col-xs-12">
		<div id="productpresets">
			<h4>{{ $__t('Presets for new products') }}</h4>

			@if(GROCY_FEATURE_FLAG_STOCK_LOCATION_TRACKING)
			<div class="form-group">
				<label for="product_presets_location_id">{{ $__t('Location') }}</label>
				<select class="form-control user-setting-control" id="product_presets_location_id" data-setting-key="product_presets_location_id">
					<option value="-1"></option>
					@foreach($locations as $location)
						<option value="{{ $location->id }}">{{ $location->name }}</option>
					@endforeach
				</select>
			</div>
			@endif

			<div class="form-group">
				<label for="product_presets_product_group_id">{{ $__t('Product group') }}</label>
				<select class="form-control user-setting-control" id="product_presets_product_group_id" data-setting-key="product_presets_product_group_id">
					<option value="-1"></option>
					@foreach($productGroups as $productGroup)
						<option value="{{ $productGroup->id }}">{{ $productGroup->name }}</option>
					@endforeach
				</select>
			</div>

			<div class="form-group">
				<label for="product_presets_qu_id">{{ $__t('Quantity unit') }}</label>
				<select class="form-control user-setting-control" id="product_presets_qu_id" data-setting-key="product_presets_qu_id">
					<option value="-1"></option>
					@foreach($quantityunits as $quantityunit)
						<option value="{{ $quantityunit->id }}">{{ $quantityunit->name }}</option>
					@endforeach
				</select>
			</div>
		</div>

		<h4 class="mt-2">{{ $__t('Stock overview') }}</h4>
		@include('components.numberpicker', array(
			'id' => 'stock_expring_soon_days',
			'additionalAttributes' => 'data-setting-key="stock_expring_soon_days"',
			'label' => 'Expiring soon days',
			'min' => 1,
			'invalidFeedback' => $__t('This cannot be lower than %s', '1'),
			'additionalCssClasses' => 'user-setting-control'
		))

		<h4 class="mt-2">{{ $__t('Purchase') }}</h4>
		@include('components.numberpicker', array(
			'id' => 'stock_default_purchase_amount',
			'additionalAttributes' => 'data-setting-key="stock_default_purchase_amount"',
			'label' => 'Default amount for purchase',
			'min' => 0,
			'invalidFeedback' => $__t('This cannot be lower than %s', '1'),
			'additionalCssClasses' => 'user-setting-control'
		))

		<h4 class="mt-2">{{ $__t('Consume') }}</h4>
		@include('components.numberpicker', array(
			'id' => 'stock_default_consume_amount',
			'additionalAttributes' => 'data-setting-key="stock_default_consume_amount"',
			'label' => 'Default amount for consume',
			'min' => 1,
			'invalidFeedback' => $__t('This cannot be lower than %s', '1'),
			'additionalCssClasses' => 'user-setting-control'
		))

		<div class="form-group">
			<div class="checkbox">
				<label for="show_icon_on_stock_overview_page_when_product_is_on_shopping_list">
					<input type="checkbox" class="user-setting-control" id="show_icon_on_stock_overview_page_when_product_is_on_shopping_list" data-setting-key="show_icon_on_stock_overview_page_when_product_is_on_shopping_list"> {{ $__t('Show an icon if the product is already on the shopping list') }}
				</label>
			</div>
		</div>

		<a href="{{ $U('/stockoverview') }}" class="btn btn-success">{{ $__t('OK') }}</a>
	</div>
</div>
@stop
