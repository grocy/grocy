@extends('layout.default')

@if($mode == 'edit')
	@section('title', $__t('Edit product'))
@else
	@section('title', $__t('Create product'))
@endif

@section('viewJsName', 'productform')

@push('pageScripts')
	<script src="{{ $U('/node_modules/TagManager/tagmanager.js?v=', true) }}{{ $version }}"></script>
@endpush

@push('pageStyles')
	<link href="{{ $U('/node_modules/TagManager/tagmanager.css?v=', true) }}{{ $version }}" rel="stylesheet">
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

			<div class="form-group">
				<label for="description">{{ $__t('Description') }}</label>
				<textarea class="form-control" rows="2" id="description" name="description">@if($mode == 'edit'){{ $product->description }}@endif</textarea>
			</div>

			<div class="form-group tm-group">
				<label for="barcode-taginput">{{ $__t('Barcode(s)') }}&nbsp;&nbsp;<i class="fas fa-barcode"></i></label>
				<input type="text" class="form-control tm-input" id="barcode-taginput">
				<div id="barcode-taginput-container"></div>
			</div>

			<div class="form-group">
				<label for="location_id">{{ $__t('Location') }}</label>
				<select required class="form-control" id="location_id" name="location_id">
					<option></option>
					@foreach($locations as $location)
						<option @if($mode == 'edit' && $location->id == $product->location_id) selected="selected" @endif value="{{ $location->id }}">{{ $location->name }}</option>
					@endforeach
				</select>
				<div class="invalid-feedback">{{ $__t('A location is required') }}</div>
			</div>

			@php if($mode == 'edit') { $value = $product->min_stock_amount; } else { $value = 0; } @endphp
			@include('components.numberpicker', array(
				'id' => 'min_stock_amount',
				'label' => 'Minimum stock amount',
				'min' => 0,
				'value' => $value,
				'invalidFeedback' => $__t('The amount cannot be lower than %s', '0')
			))

			@php if($mode == 'edit') { $value = $product->default_best_before_days; } else { $value = 0; } @endphp
			@include('components.numberpicker', array(
				'id' => 'default_best_before_days',
				'label' => 'Default best before days',
				'min' => -1,
				'value' => $value,
				'invalidFeedback' => $__t('The amount cannot be lower than %s', '-1'),
				'hint' => $__t('For purchases this amount of days will be added to today for the best before date suggestion') . ' (' . $__t('-1 means that this product never expires') . ')'
			))

			@php if($mode == 'edit') { $value = $product->default_best_before_days_after_open; } else { $value = 0; } @endphp
			@include('components.numberpicker', array(
				'id' => 'default_best_before_days_after_open',
				'label' => 'Default best before days after opened',
				'min' => 0,
				'value' => $value,
				'invalidFeedback' => $__t('The amount cannot be lower than %s', '-1'),
				'hint' => $__t('When a product was marked as opened, the best before date will be replaced by today + this amount of days (a value of 0 disables this)')
			))

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
						<option @if($mode == 'edit' && $quantityunit->id == $product->qu_id_stock) selected="selected" @endif value="{{ $quantityunit->id }}">{{ $quantityunit->name }}</option>
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

			<div class="form-group">
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
		<label class="mt-2">{{ $__t('Picture') }}</label>
		<button id="delete-current-product-picture-button" class="btn btn-sm btn-danger @if(empty($product->picture_file_name)) disabled @endif"><i class="fas fa-trash"></i> {{ $__t('Delete') }}</button>
		@if(!empty($product->picture_file_name))
			<p><img id="current-product-picture" src="{{ $U('/api/files/productpictures/' . base64_encode($product->picture_file_name)) }}" class="img-fluid img-thumbnail mt-2"></p>
			<p id="delete-current-product-picture-on-save-hint" class="form-text text-muted font-italic d-none">{{ $__t('The current picture will be deleted when you save the product') }}</p>
		@else
			<p id="no-current-product-picture-hint" class="form-text text-muted font-italic">{{ $__t('No picture available') }}</p>
		@endif
	</div>
</div>
@stop
