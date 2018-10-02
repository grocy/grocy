@extends('layout.default')

@if($mode == 'edit')
	@section('title', $L('Edit product'))
@else
	@section('title', $L('Create product'))
@endif

@section('viewJsName', 'productform')

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
				<label for="name">{{ $L('Name') }}</label>
				<input type="text" class="form-control" required id="name" name="name" value="@if($mode == 'edit'){{ $product->name}}@endif">
				<div class="invalid-feedback">{{ $L('A name is required') }}</div>
			</div>

			<div class="form-group">
				<label for="description">{{ $L('Description') }}</label>
				<textarea class="form-control" rows="2" id="description" name="description">@if($mode == 'edit'){{ $product->description }}@endif</textarea>
			</div>

			<div class="form-group tm-group">
				<label for="barcode-taginput">{{ $L('Barcode(s)') }}&nbsp;&nbsp;<i class="fas fa-barcode"></i></label>
				<input type="text" class="form-control tm-input" id="barcode-taginput">
				<div id="barcode-taginput-container"></div>
			</div>

			<div class="form-group">
				<label for="location_id">{{ $L('Location') }}</label>
				<select required class="form-control" id="location_id" name="location_id">
					@foreach($locations as $location)
						<option @if($mode == 'edit' && $location->id == $product->location_id) selected="selected" @endif value="{{ $location->id }}">{{ $location->name }}</option>
					@endforeach
				</select>
				<div class="invalid-feedback">{{ $L('A location is required') }}</div>
			</div>

			@php if($mode == 'edit') { $value = $product->min_stock_amount; } else { $value = 0; } @endphp
			@include('components.numberpicker', array(
				'id' => 'min_stock_amount',
				'label' => 'Minimum stock amount',
				'min' => 0,
				'value' => $value,
				'invalidFeedback' => $L('The amount cannot be lower than #1', '0')
			))

			@php if($mode == 'edit') { $value = $product->default_best_before_days; } else { $value = 0; } @endphp
			@include('components.numberpicker', array(
				'id' => 'default_best_before_days',
				'label' => 'Default best before days',
				'min' => -1,
				'value' => $value,
				'invalidFeedback' => $L('The amount cannot be lower than #1', '-1'),
				'hint' => $L('For purchases this amount of days will be added to today for the best before date suggestion') . ' (' . $L('-1 means that this product never expires') . ')'
			))

			<div class="form-group">
				<label for="product_group_id">{{ $L('Product group') }}</label>
				<select class="form-control" id="product_group_id" name="product_group_id">
					<option></option>
					@foreach($productgroups as $productgroup)
						<option @if($mode == 'edit' && $productgroup->id == $product->product_group_id) selected="selected" @endif value="{{ $productgroup->id }}">{{ $productgroup->name }}</option>
					@endforeach
				</select>
			</div>

			<div class="form-group">
				<label for="qu_id_purchase">{{ $L('Quantity unit purchase') }}</label>
				<select required class="form-control input-group-qu" id="qu_id_purchase" name="qu_id_purchase">
					@foreach($quantityunits as $quantityunit)
						<option @if($mode == 'edit' && $quantityunit->id == $product->qu_id_purchase) selected="selected" @endif value="{{ $quantityunit->id }}">{{ $quantityunit->name }}</option>
					@endforeach
				</select>
				<div class="invalid-feedback">{{ $L('A quantity unit is required') }}</div>
			</div>

			<div class="form-group">
				<label for="qu_id_stock">{{ $L('Quantity unit stock') }}</label>
				<select required class="form-control input-group-qu" id="qu_id_stock" name="qu_id_stock">
					@foreach($quantityunits as $quantityunit)
						<option @if($mode == 'edit' && $quantityunit->id == $product->qu_id_stock) selected="selected" @endif value="{{ $quantityunit->id }}">{{ $quantityunit->name }}</option>
					@endforeach
				</select>
				<div class="invalid-feedback">{{ $L('A quantity unit is required') }}</div>
			</div>

			@php if($mode == 'edit') { $value = $product->qu_factor_purchase_to_stock; } else { $value = 1; } @endphp
			@include('components.numberpicker', array(
				'id' => 'qu_factor_purchase_to_stock',
				'label' => 'Factor purchase to stock quantity unit',
				'min' => 1,
				'value' => $value,
				'invalidFeedback' => $L('The amount cannot be lower than #1', '1'),
				'additionalCssClasses' => 'input-group-qu',
				'additionalHtmlElements' => '<p id="qu-conversion-info" class="form-text text-muted small d-none"></p>'
			))

			<div class="form-group">
				<label for="product-picture">{{ $L('Product picture') }}</label>
				<div class="custom-file">
					<input type="file" class="custom-file-input" id="product-picture" accept="image/*">
					<label class="custom-file-label" for="product-picture">{{ $L('No file selected') }}</label>
				</div>
				<p class="form-text text-muted small">{{ $L('If you don\'t select a file, the current picture will not be altered') }}</p>
			</div>

			<button id="save-product-button" class="btn btn-success">{{ $L('Save') }}</button>
		</form>
	</div>

	<div class="col-lg-6 col-xs-12">
		<label class="mt-2">{{ $L('Current picture') }}</label>
		<button id="delete-current-product-picture-button" class="btn btn-sm btn-danger @if(empty($product->picture_file_name)) disabled @endif"><i class="fas fa-trash"></i> {{ $L('Delete') }}</button>
		@if(!empty($product->picture_file_name))
			<p><img id="current-product-picture" src="{{ $U('/api/file/productpictures?file_name=' . $product->picture_file_name) }}" class="img-fluid img-thumbnail mt-2"></p>
			<p id="delete-current-product-picture-on-save-hint" class="form-text text-muted font-italic d-none">{{ $L('The current picture will be deleted when you save the product') }}</p>
		@else
			<p id="no-current-product-picture-hint" class="form-text text-muted font-italic">{{ $L('No picture') }}</p>
		@endif
	</div>
</div>
@stop
