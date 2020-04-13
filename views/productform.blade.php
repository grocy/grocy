@extends('layout.default')

@if($mode == 'edit')
	@section('title', $__t('Edit product'))
@else
	@section('title', $__t('Create product'))
@endif

@section('viewJsName', 'productform')

@push('pageScripts')
	<script src="{{ $U('/node_modules/TagManager/tagmanager.js?v=', true) }}{{ $version }}"></script>
	<script src="{{ $U('/node_modules/datatables.net-rowgroup/js/dataTables.rowGroup.min.js?v=', true) }}{{ $version }}"></script>
	<script src="{{ $U('/node_modules/datatables.net-rowgroup-bs4/js/rowGroup.bootstrap4.min.js?v=', true) }}{{ $version }}"></script>
@endpush

@push('pageStyles')
	<link href="{{ $U('/node_modules/TagManager/tagmanager.css?v=', true) }}{{ $version }}" rel="stylesheet">
	<link href="{{ $U('/node_modules/datatables.net-rowgroup-bs4/css/rowGroup.bootstrap4.min.css?v=', true) }}{{ $version }}" rel="stylesheet">
@endpush

@section('content')
<div class="row">

	<div class="col-lg-6 col-xs-12">
		<h1>@yield('title')</h1>

		<script>Grocy.EditMode = '{{ $mode }}';</script>

		@if($mode == 'edit')
			<script>Grocy.EditObjectId = {{ $product->id }};</script>

			@if(!empty($product->picture_file_name))
				<script>Grocy.ProductPictureFileName = '{{ $product->picture_file_name }}';</script>
			@endif
		@endif

		<form id="product-form" novalidate>

			<div class="form-group">
				<label for="name">{{ $__t('Name') }}</label>
				<input type="text" class="form-control" required id="name" name="name" value="@if($mode == 'edit'){{ $product->name}}@endif">
				<div class="invalid-feedback">{{ $__t('A name is required') }}</div>
			</div>

			@php $prefillById = ''; if($mode=='edit') { $prefillById = $product->parent_product_id; } @endphp
			@php
				$hint = '';
				if ($isSubProductOfOthers)
				{
					$hint = $__t('Not possible because this product is already used as a parent product in another product');
				}
			@endphp
			@include('components.productpicker', array(
				'products' => $products,
				'nextInputSelector' => '#barcode-taginput',
				'prefillById' => $prefillById,
				'disallowAllProductWorkflows' => true,
				'isRequired' => false,
				'label' => 'Parent product',
				'disabled' => $isSubProductOfOthers,
				'hint' => $hint
			))

			<div class="form-group">
				<label for="description">{{ $__t('Description') }}</label>
				<textarea class="form-control wysiwyg-editor" id="description" name="description">@if($mode == 'edit'){{ $product->description }}@endif</textarea>
			</div>

			<div class="form-group tm-group">
				<label for="barcode-taginput">{{ $__t('Barcode(s)') }}&nbsp;&nbsp;<i class="fas fa-barcode"></i></label>
				<div class="input-group">
					<input type="text" class="form-control tm-input barcodescanner-input" id="barcode-taginput" data-target="#barcode-taginput">
				</div>
				<div id="barcode-taginput-container"></div>
			</div>

			@if(GROCY_FEATURE_FLAG_STOCK_LOCATION_TRACKING)
			<div class="form-group">
				<label for="location_id">{{ $__t('Default location') }}</label>
				<select required class="form-control" id="location_id" name="location_id">
					<option></option>
					@foreach($locations as $location)
						<option @if($mode == 'edit' && $location->id == $product->location_id) selected="selected" @endif value="{{ $location->id }}">{{ $location->name }}</option>
					@endforeach
				</select>
				<div class="invalid-feedback">{{ $__t('A location is required') }}</div>
			</div>
			@else
			<input type="hidden" name="location_id" id="location_id" value="1">
			@endif

			@if(GROCY_FEATURE_FLAG_STOCK_PRICE_TRACKING)
			@include('components.shoppinglocationpicker', array(
				'label' => 'Default store',
				'shoppinglocations' => $shoppinglocations
			))
			@else
			<input type="hidden" name="shopping_location_id" id="shopping_location_id" value="1">
			@endif

			@php if($mode == 'edit') { $value = $product->min_stock_amount; } else { $value = 0; } @endphp
			@include('components.numberpicker', array(
				'id' => 'min_stock_amount',
				'label' => 'Minimum stock amount',
				'min' => 0,
				'value' => $value,
				'invalidFeedback' => $__t('The amount cannot be lower than %s', '0'),
				'additionalGroupCssClasses' => 'mb-1'
			))

			<div class="form-group">
				<div class="form-check">
					<input type="hidden" name="cumulate_min_stock_amount_of_sub_products" value="0">
					<input @if($mode == 'edit' && $product->cumulate_min_stock_amount_of_sub_products == 1) checked @endif class="form-check-input" type="checkbox" id="cumulate_min_stock_amount_of_sub_products" name="cumulate_min_stock_amount_of_sub_products" value="1">
					<label class="form-check-label" for="cumulate_min_stock_amount_of_sub_products">{{ $__t('Accumulate sub products min. stock amount') }}
						<span class="text-muted small">{{ $__t('If enabled, the min. stock amount of sub products will be accumulated into this product, means the sub product will never be "missing", only this product') }}</span>
					</label>
				</div>
			</div>

			@if(GROCY_FEATURE_FLAG_STOCK_BEST_BEFORE_DATE_TRACKING)
			@php if($mode == 'edit') { $value = $product->default_best_before_days; } else { $value = 0; } @endphp
			@include('components.numberpicker', array(
				'id' => 'default_best_before_days',
				'label' => 'Default best before days',
				'min' => -1,
				'value' => $value,
				'invalidFeedback' => $__t('The amount cannot be lower than %s', '-1'),
				'hint' => $__t('For purchases this amount of days will be added to today for the best before date suggestion') . ' (' . $__t('-1 means that this product never expires') . ')'
			))

			@if(GROCY_FEATURE_FLAG_STOCK_PRODUCT_OPENED_TRACKING)
			@php if($mode == 'edit') { $value = $product->default_best_before_days_after_open; } else { $value = 0; } @endphp
			@include('components.numberpicker', array(
				'id' => 'default_best_before_days_after_open',
				'label' => 'Default best before days after opened',
				'min' => 0,
				'value' => $value,
				'invalidFeedback' => $__t('The amount cannot be lower than %s', '-1'),
				'hint' => $__t('When this product was marked as opened, the best before date will be replaced by today + this amount of days (a value of 0 disables this)')
			))
			@endif
			@endif

			<div class="form-group">
				<label for="product_group_id">{{ $__t('Product group') }}</label>
				<select class="form-control" id="product_group_id" name="product_group_id">
					<option></option>
					@foreach($productgroups as $productgroup)
						<option @if($mode == 'edit' && $productgroup->id == $product->product_group_id) selected="selected" @endif value="{{ $productgroup->id }}">{{ $productgroup->name }}</option>
					@endforeach
				</select>
			</div>

			<div class="form-group">
				<label for="qu_id_purchase">{{ $__t('Quantity unit purchase') }}</label>
				<select required class="form-control input-group-qu" id="qu_id_purchase" name="qu_id_purchase">
					<option></option>
					@foreach($quantityunits as $quantityunit)
						<option @if($mode == 'edit' && $quantityunit->id == $product->qu_id_purchase) selected="selected" @endif value="{{ $quantityunit->id }}">{{ $quantityunit->name }}</option>
					@endforeach
				</select>
				<div class="invalid-feedback">{{ $__t('A quantity unit is required') }}</div>
			</div>

			<div class="form-group">
				<label for="qu_id_stock">{{ $__t('Quantity unit stock') }}</label>
				<select required class="form-control input-group-qu" id="qu_id_stock" name="qu_id_stock">
					<option></option>
					@foreach($quantityunits as $quantityunit)
						<option @if($mode == 'edit' && $quantityunit->id == $product->qu_id_stock) selected="selected" @endif value="{{ $quantityunit->id }}" data-plural-form="{{ $quantityunit->name_plural }}">{{ $quantityunit->name }}</option>
					@endforeach
				</select>
				<div class="invalid-feedback">{{ $__t('A quantity unit is required') }}</div>
			</div>

			@php if($mode == 'edit') { $value = $product->qu_factor_purchase_to_stock; } else { $value = 1; } @endphp
			@include('components.numberpicker', array(
				'id' => 'qu_factor_purchase_to_stock',
				'label' => 'Factor purchase to stock quantity unit',
				'min' => 1,
				'value' => $value,
				'invalidFeedback' => $__t('The amount cannot be lower than %s', '1'),
				'additionalCssClasses' => 'input-group-qu',
				'additionalHtmlElements' => '<p id="qu-conversion-info" class="form-text text-muted small d-none"></p>'
			))

			<div class="form-group">
				<div class="form-check">
					<input type="hidden" name="allow_partial_units_in_stock" value="0">
					<input @if($mode == 'edit' && $product->allow_partial_units_in_stock == 1) checked @endif class="form-check-input" type="checkbox" id="allow_partial_units_in_stock" name="allow_partial_units_in_stock" value="1">
					<label class="form-check-label" for="allow_partial_units_in_stock">{{ $__t('Allow partial units in stock') }}</label>
				</div>
			</div>

			<div class="form-group mb-1">
				<div class="form-check">
					<input type="hidden" name="enable_tare_weight_handling" value="0">
					<input @if($mode == 'edit' && $product->enable_tare_weight_handling == 1) checked @endif class="form-check-input" type="checkbox" id="enable_tare_weight_handling" name="enable_tare_weight_handling" value="1">
					<label class="form-check-label" for="enable_tare_weight_handling">{{ $__t('Enable tare weight handling') }}
						<span class="text-muted small">{{ $__t('This is useful e.g. for flour in jars - on purchase/consume/inventory you always weigh the whole jar, the amount to be posted is then automatically calculated based on what is in stock and the tare weight defined below') }}</span>
					</label>
				</div>
			</div>

			@php if($mode == 'edit') { $value = $product->tare_weight; } else { $value = 0; } @endphp
			@php if(($mode == 'edit' && $product->enable_tare_weight_handling == 0) || $mode == 'create') { $additionalAttributes = 'disabled'; } else { $additionalAttributes = ''; } @endphp
			@include('components.numberpicker', array(
				'id' => 'tare_weight',
				'label' => 'Tare weight',
				'min' => 0,
				'step' => 0.01,
				'value' => $value,
				'invalidFeedback' => $__t('This cannot be lower than %s', '0'),
				'additionalAttributes' => $additionalAttributes,
				'hintId' => 'tare_weight_qu_info'
			))
			@php $additionalAttributes = '' @endphp

			@if(GROCY_FEATURE_FLAG_RECIPES)
			<div class="form-group">
				<div class="form-check">
					<input type="hidden" name="not_check_stock_fulfillment_for_recipes" value="0">
					<input @if($mode == 'edit' && $product->not_check_stock_fulfillment_for_recipes == 1) checked @endif class="form-check-input" type="checkbox" id="not_check_stock_fulfillment_for_recipes" name="not_check_stock_fulfillment_for_recipes" value="1">
					<label class="form-check-label" for="not_check_stock_fulfillment_for_recipes">{{ $__t('Disable stock fulfillment checking for this ingredient') }}
						<span class="text-muted small">{{ $__t('This will be used as the default setting when adding this product as a recipe ingredient') }}</span>
					</label>
				</div>
			</div>

			@php if($mode == 'edit') { $value = $product->calories; } else { $value = 0; } @endphp
			@include('components.numberpicker', array(
				'id' => 'calories',
				'label' => 'Energy (kcal)',
				'min' => 0,
				'step' => 0.01,
				'value' => $value,
				'invalidFeedback' => $__t('The amount cannot be lower than %s', '0'),
				'hint' => $__t('Per stock quantity unit'),
				'isRequired' => false
			))
			@endif

			@if(GROCY_FEATURE_FLAG_STOCK_PRODUCT_FREEZING)
			@php if($mode == 'edit') { $value = $product->default_best_before_days_after_freezing; } else { $value = 0; } @endphp
			@include('components.numberpicker', array(
				'id' => 'default_best_before_days_after_freezing',
				'label' => 'Default best before days after freezing',
				'min' => -1,
				'value' => $value,
				'invalidFeedback' => $__t('The amount cannot be lower than %s', '0'),
				'hint' => $__t('On moving this product to a freezer location (so when freezing it), the best before date will be replaced by today + this amount of days')
			))

			@php if($mode == 'edit') { $value = $product->default_best_before_days_after_thawing; } else { $value = 0; } @endphp
			@include('components.numberpicker', array(
				'id' => 'default_best_before_days_after_thawing',
				'label' => 'Default best before days after thawing',
				'min' => -1,
				'value' => $value,
				'invalidFeedback' => $__t('The amount cannot be lower than %s', '0'),
				'hint' => $__t('On moving this product from a freezer location (so when thawing it), the best before date will be replaced by today + this amount of days')
			))
			@else
			<input type="hidden" name="default_best_before_days_after_freezing" value="0">
			<input type="hidden" name="default_best_before_days_after_thawing" value="0">
			@endif

			<div class="form-group">
				<label for="product-picture">{{ $__t('Product picture') }}
					<span class="text-muted small">{{ $__t('If you don\'t select a file, the current picture will not be altered') }}</span>
				</label>
				<div class="custom-file">
					<input type="file" class="custom-file-input" id="product-picture" accept="image/*">
					<label class="custom-file-label" for="product-picture">{{ $__t('No file selected') }}</label>
				</div>
			</div>

			@include('components.userfieldsform', array(
				'userfields' => $userfields,
				'entity' => 'products'
			))

			<button id="save-product-button" class="btn btn-success">{{ $__t('Save') }}</button>
		</form>

	</div>

	<div class="col-lg-6 col-xs-12">
		<h2>
			{{ $__t('QU conversions') }}
			<a id="qu-conversion-add-button" class="btn btn-outline-dark" href="#">
				<i class="fas fa-plus"></i> {{ $__t('Add') }}
			</a>
		</h2>
		<h5 id="qu-conversion-headline-info" class="text-muted font-italic"></h5>
		<table id="qu-conversions-table" class="table table-sm table-striped dt-responsive">
			<thead>
				<tr>
					<th class="border-right"></th>
					<th>{{ $__t('Factor') }}</th>
					<th>{{ $__t('Unit') }}</th>
					<th class="d-none">Hidden group</th>
					<th class="d-none">Hidden from_qu_id</th>
				</tr>
			</thead>
			<tbody class="d-none">
				@if($mode == "edit")
				@foreach($quConversions as $quConversion)
					@if($quConversion->product_id == $product->id || $quConversion->product_id == null)
					<tr>
						<td class="fit-content border-right">
							<a class="btn btn-sm btn-info qu-conversion-edit-button @if($quConversion->product_id == null) disabled @endif" href="#" data-qu-conversion-id="{{ $quConversion->id }}">
								<i class="fas fa-edit"></i>
							</a>
							<a class="btn btn-sm btn-danger qu-conversion-delete-button @if($quConversion->product_id == null) disabled @endif" href="#" data-qu-conversion-id="{{ $quConversion->id }}">
								<i class="fas fa-trash"></i>
							</a>
						</td>
						<td>
							<span class="locale-number locale-number-quantity-amount">{{ $quConversion->factor }}</span>
						</td>
						<td>
							{{ FindObjectInArrayByPropertyValue($quantityunits, 'id', $quConversion->to_qu_id)->name }}
						</td>
						<td class="d-none">
							@if($quConversion->product_id != null)
							{{ $__t('Product overrides') }}
							@else
							{{ $__t('Default conversions') }}
							@endif
						</td>
						<td class="d-none">
							from_qu_id xx{{ $quConversion->from_qu_id }}xx
						</td>
					</tr>
					@endif
				@endforeach
				@endif
			</tbody>
		</table>

		<div class="pt-5">
			<label class="mt-2">{{ $__t('Picture') }}</label>
			<button id="delete-current-product-picture-button" class="btn btn-sm btn-danger @if(empty($product->picture_file_name)) disabled @endif"><i class="fas fa-trash"></i> {{ $__t('Delete') }}</button>
			@if(!empty($product->picture_file_name))
				<p><img id="current-product-picture" data-src="{{ $U('/api/files/productpictures/' . base64_encode($product->picture_file_name) . '?force_serve_as=picture&best_fit_width=400') }}" class="img-fluid img-thumbnail mt-2 lazy"></p>
				<p id="delete-current-product-picture-on-save-hint" class="form-text text-muted font-italic d-none">{{ $__t('The current picture will be deleted when you save the product') }}</p>
			@else
				<p id="no-current-product-picture-hint" class="form-text text-muted font-italic">{{ $__t('No picture available') }}</p>
			@endif
		</div>
	</div>
</div>
@stop
