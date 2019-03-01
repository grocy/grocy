@extends('layout.default')

@section('title', $L('Stock settings'))

@section('viewJsName', 'stocksettings')

@section('content')
<div class="row">
	<div class="col-lg-6 col-xs-12">
		<h1>@yield('title')</h1>

		<div id="productpresets">
			<h4>{{ $L('Presets for new products') }}</h4>

			<div class="form-group">
				<label for="product_presets_location_id">{{ $L('Location') }}</label>
				<select class="form-control user-setting-control" id="product_presets_location_id" data-setting-key="product_presets_location_id">
					<option value="-1"></option>
					@foreach($locations as $location)
						<option value="{{ $location->id }}">{{ $location->name }}</option>
					@endforeach
				</select>
			</div>

			<div class="form-group">
				<label for="product_presets_product_group_id">{{ $L('Product group') }}</label>
				<select class="form-control user-setting-control" id="product_presets_product_group_id" data-setting-key="product_presets_product_group_id">
					<option value="-1"></option>
					@foreach($productGroups as $productGroup)
						<option value="{{ $productGroup->id }}">{{ $productGroup->name }}</option>
					@endforeach
				</select>
			</div>

			<div class="form-group">
				<label for="product_presets_qu_id">{{ $L('Quantity unit') }}</label>
				<select class="form-control user-setting-control" id="product_presets_qu_id" data-setting-key="product_presets_qu_id">
					<option value="-1"></option>
					@foreach($quantityunits as $quantityunit)
						<option value="{{ $quantityunit->id }}">{{ $quantityunit->name }}</option>
					@endforeach
				</select>
			</div>
		</div>

		@if(GROCY_FEATURE_FLAG_SHOPPINGLIST)
		<h4 class="mt-2">{{ $L('Shopping list to stock workflow') }}</h4>
		
		<div class="form-group">
			<div class="checkbox">
				<label for="shopping-list-to-stock-workflow-auto-submit-when-prefilled">
					<input type="checkbox" class="user-setting-control" id="shopping-list-to-stock-workflow-auto-submit-when-prefilled" name="shopping-list-to-stock-workflow-auto-submit-when-prefilled" data-setting-key="shopping_list_to_stock_workflow_auto_submit_when_prefilled"> {{ $L('Automatically do the booking using the last price and the amount of the shopping list item, if the product has "Default best before days" set') }}
				</label>
			</div>
		</div>
		@endif

		<a href="{{ $U('/products') }}" class="btn btn-success">{{ $L('OK') }}</a>
	</div>
</div>
@stop
