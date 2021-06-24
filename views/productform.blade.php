@extends($rootLayout)

@if($mode == 'edit')
@section('title', $__t('Edit product'))
@else
@section('title', $__t('Create product'))
@endif

@section('viewJsName', 'productform')

@section('grocyConfigProps')
EditMode: '{{ $mode }}',

@if($mode == 'edit')
		EditObjectId: {{ $product->id }},

		@if(!empty($product->picture_file_name))
			ProductPictureFileName: '{{ $product->picture_file_name }}',
		@endif
@endif
@endsection

@section('content')
<div class="row">
	<div class="col">
		<h2 class="title">@yield('title')</h2>
	</div>
</div>

<hr class="my-2">

<div class="row">
	<div class="col-lg-6 col-12">
		<form id="product-form"
			novalidate>

			<div class="form-group">
				<label for="name">{{ $__t('Name') }}</label>
				<input type="text"
					class="form-control"
					required
					id="name"
					name="name"
					value="@if($mode == 'edit'){{ $product->name}}@endif">
				<div class="invalid-feedback">{{ $__t('A name is required') }}</div>
			</div>

			<div class="form-group">
				<div class="custom-control custom-checkbox">
					<input @if($mode=='create'
						)
						checked
						@elseif($mode=='edit'
						&&
						$product->active == 1) checked @endif class="form-check-input custom-control-input" type="checkbox" id="active" name="active" value="1">
					<label class="form-check-label custom-control-label"
						for="active">{{ $__t('Active') }}</label>
				</div>
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
			'prefillById' => $prefillById,
			'disallowAllProductWorkflows' => true,
			'isRequired' => false,
			'label' => 'Parent product',
			'disabled' => $isSubProductOfOthers,
			'hint' => $hint
			))
			@php $hint = ''; @endphp

			<div class="form-group">
				<label for="description">{{ $__t('Description') }}</label>
				<textarea class="form-control wysiwyg-editor"
					id="description"
					name="description">@if($mode == 'edit'){{ $product->description }}@endif</textarea>
			</div>

			@if(GROCY_FEATURE_FLAG_STOCK_LOCATION_TRACKING)
			<div class="form-group">
				<label for="location_id">{{ $__t('Default location') }}</label>
				<select required
					class="custom-control custom-select"
					id="location_id"
					name="location_id">
					<option></option>
					@foreach($locations as $location)
					<option @if($mode=='edit'
						&&
						$location->id == $product->location_id) selected="selected" @endif value="{{ $location->id }}">{{ $location->name }}</option>
					@endforeach
				</select>
				<div class="invalid-feedback">{{ $__t('A location is required') }}</div>
			</div>
			@else
			<input type="hidden"
				name="location_id"
				id="location_id"
				value="1">
			@endif

			@php $prefillById = ''; if($mode=='edit') { $prefillById = $product->shopping_location_id; } @endphp
			@if(GROCY_FEATURE_FLAG_STOCK_PRICE_TRACKING)
			@include('components.shoppinglocationpicker', array(
			'label' => 'Default store',
			'prefillById' => $prefillById,
			'shoppinglocations' => $shoppinglocations
			))
			@else
			<input type="hidden"
				name="shopping_location_id"
				id="shopping_location_id"
				value="1">
			@endif

			@php if($mode == 'edit') { $value = $product->min_stock_amount; } else { $value = 0; } @endphp
			@include('components.numberpicker', array(
			'id' => 'min_stock_amount',
			'label' => 'Minimum stock amount',
			'min' => '0.',
			'decimals' => $userSettings['stock_decimal_places_amounts'],
			'value' => $value,
			'additionalGroupCssClasses' => 'mb-1',
			'additionalCssClasses' => 'locale-number-input locale-number-quantity-amount'
			))

			<div class="form-group">
				<div class="custom-control custom-checkbox">
					<input @if($mode=='edit'
						&&
						$product->cumulate_min_stock_amount_of_sub_products == 1) checked @endif class="form-check-input custom-control-input" type="checkbox" id="cumulate_min_stock_amount_of_sub_products" name="cumulate_min_stock_amount_of_sub_products" value="1">
					<label class="form-check-label custom-control-label"
						for="cumulate_min_stock_amount_of_sub_products">{{ $__t('Accumulate sub products min. stock amount') }}
						&nbsp;<i class="fas fa-question-circle text-muted"
							data-toggle="tooltip"
							title="{{ $__t('If enabled, the min. stock amount of sub products will be accumulated into this product, means the sub product will never be "missing", only this product') }}"></i>
					</label>
				</div>
			</div>

			@if(GROCY_FEATURE_FLAG_STOCK_BEST_BEFORE_DATE_TRACKING)
			<div class="form-group">
				<label class="d-block my-0"
					for="location_id">{{ $__t('Due date type') }}
					<i class="fas fa-question-circle text-muted"
						data-toggle="tooltip"
						title="{{ $__t('Based on the selected type, the highlighting on the stock overview page will be different') }}"></i>
				</label>
				<div class="custom-control custom-radio mt-n2">
					<input class="custom-control-input"
						type="radio"
						name="due_type"
						id="due-type-bestbefore"
						value="1"
						@if($mode=='edit'
						&&
						$product->due_type == 1) checked @else checked @endif>
					<label class="custom-control-label"
						for="due-type-bestbefore">{{ $__t('Best before date') }}
						<i class="fas fa-question-circle text-muted"
							data-toggle="tooltip"
							title="{{ $__t('Means that the product is maybe still safe to be consumed after its due date is reached') }}"></i>
					</label>
				</div>
				<div class="custom-control custom-radio mt-n2">
					<input class="custom-control-input"
						type="radio"
						name="due_type"
						id="due-type-expiration"
						value="2"
						@if($mode=='edit'
						&&
						$product->due_type == 2) checked @endif>
					<label class="custom-control-label"
						for="due-type-expiration">{{ $__t('Expiration date') }}
						<i class="fas fa-question-circle text-muted"
							data-toggle="tooltip"
							title="{{ $__t('Means that the product is not safe to be consumed after its due date is reached') }}"></i>
					</label>
				</div>
			</div>

			@php if($mode == 'edit') { $value = $product->default_best_before_days; } else { $value = 0; } @endphp
			@include('components.numberpicker', array(
			'id' => 'default_best_before_days',
			'label' => 'Default due days',
			'min' => -1,
			'value' => $value,
			'hint' => $__t('For purchases this amount of days will be added to today for the due date suggestion') . ' (' . $__t('-1 means that this product will be never overdue') . ')'
			))

			@if(GROCY_FEATURE_FLAG_STOCK_PRODUCT_OPENED_TRACKING)
			@php if($mode == 'edit') { $value = $product->default_best_before_days_after_open; } else { $value = 0; } @endphp
			@include('components.numberpicker', array(
			'id' => 'default_best_before_days_after_open',
			'label' => 'Default due days after opened',
			'min' => 0,
			'value' => $value,
			'hint' => $__t('When this product was marked as opened, the due date will be replaced by today + this amount of days (a value of 0 disables this)')
			))
			@else
			<input type="hidden"
				name="default_best_before_days_after_open"
				id="default_best_before_days_after_open"
				value="1">
			@endif
			@else
			<input type="hidden"
				name="default_best_before_days"
				id="default_best_before_days"
				value="1">
			<input type="hidden"
				name="due_type"
				id="due_type"
				value="1">
			@endif

			<div class="form-group">
				<label for="product_group_id">{{ $__t('Product group') }}</label>
				<select class="custom-control custom-select"
					id="product_group_id"
					name="product_group_id">
					<option></option>
					@foreach($productgroups as $productgroup)
					<option @if($mode=='edit'
						&&
						$productgroup->id == $product->product_group_id) selected="selected" @endif value="{{ $productgroup->id }}">{{ $productgroup->name }}</option>
					@endforeach
				</select>
			</div>

			<div class="form-group">
				<label for="qu_id_stock">{{ $__t('Quantity unit stock') }}</label>
				<i class="fas fa-question-circle text-muted"
					data-toggle="tooltip"
					title="{{ $__t('Quantity unit stock cannot be changed after first purchase') }}"></i>
				<select required
					class="custom-control custom-select input-group-qu"
					id="qu_id_stock"
					name="qu_id_stock"
					@if($mode=='edit'
					)
					disabled
					@endif>
					<option></option>
					@foreach($quantityunits as $quantityunit)
					<option @if($mode=='edit'
						&&
						$quantityunit->id == $product->qu_id_stock) selected="selected" @endif value="{{ $quantityunit->id }}" data-plural-form="{{ $quantityunit->name_plural }}">{{ $quantityunit->name }}</option>
					@endforeach
				</select>
				<div class="invalid-feedback">{{ $__t('A quantity unit is required') }}</div>
			</div>

			<div class="form-group">
				<label for="qu_id_purchase">{{ $__t('Default quantity unit purchase') }}</label>
				<i class="fas fa-question-circle text-muted"
					data-toggle="tooltip"
					title="{{ $__t('This is the default quantity unit used when adding this product to the shopping list') }}"></i>
				<select required
					class="custom-control custom-select input-group-qu"
					id="qu_id_purchase"
					name="qu_id_purchase">
					<option></option>
					@foreach($quantityunits as $quantityunit)
					<option @if($mode=='edit'
						&&
						$quantityunit->id == $product->qu_id_purchase) selected="selected" @endif value="{{ $quantityunit->id }}">{{ $quantityunit->name }}</option>
					@endforeach
				</select>
				<div class="invalid-feedback">{{ $__t('A quantity unit is required') }}</div>
			</div>

			@php if($mode == 'edit') { $value = $product->qu_factor_purchase_to_stock; } else { $value = 1; } @endphp
			@include('components.numberpicker', array(
			'id' => 'qu_factor_purchase_to_stock',
			'label' => 'Factor purchase to stock quantity unit',
			'min' => $DEFAULT_MIN_AMOUNT,
			'decimals' => $userSettings['stock_decimal_places_amounts'],
			'value' => $value,
			'additionalCssClasses' => 'input-group-qu locale-number-input locale-number-quantity-amount',
			'additionalHtmlElements' => '<p id="qu-conversion-info"
				class="form-text text-info d-none"></p>'
			))

			<div class="form-group mb-1">
				<div class="custom-control custom-checkbox">
					<input @if($mode=='edit'
						&&
						$product->enable_tare_weight_handling == 1) checked @endif class="form-check-input custom-control-input" type="checkbox" id="enable_tare_weight_handling" name="enable_tare_weight_handling" value="1">
					<label class="form-check-label custom-control-label"
						for="enable_tare_weight_handling">{{ $__t('Enable tare weight handling') }}
						&nbsp;<i class="fas fa-question-circle text-muted"
							data-toggle="tooltip"
							title="{{ $__t('This is useful e.g. for flour in jars - on purchase/consume/inventory you always weigh the whole jar, the amount to be posted is then automatically calculated based on what is in stock and the tare weight defined below') }}"></i>
					</label>
				</div>
			</div>

			@php if($mode == 'edit') { $value = $product->tare_weight; } else { $value = 0; } @endphp
			@php if(($mode == 'edit' && $product->enable_tare_weight_handling == 0) || $mode == 'create') { $additionalAttributes = 'disabled'; } else { $additionalAttributes = ''; } @endphp
			@include('components.numberpicker', array(
			'id' => 'tare_weight',
			'label' => 'Tare weight',
			'min' => 0,
			'decimals' => $userSettings['stock_decimal_places_amounts'],
			'value' => $value,
			'additionalAttributes' => $additionalAttributes,
			'contextInfoId' => 'tare_weight_qu_info',
			'additionalCssClasses' => 'locale-number-input locale-number-quantity-amount'
			))
			@php $additionalAttributes = '' @endphp

			@if(GROCY_FEATURE_FLAG_RECIPES)
			<div class="form-group">
				<div class="custom-control custom-checkbox">
					<input @if($mode=='edit'
						&&
						$product->not_check_stock_fulfillment_for_recipes == 1) checked @endif class="form-check-input custom-control-input" type="checkbox" id="not_check_stock_fulfillment_for_recipes" name="not_check_stock_fulfillment_for_recipes" value="1">
					<label class="form-check-label custom-control-label"
						for="not_check_stock_fulfillment_for_recipes">{{ $__t('Disable stock fulfillment checking for this ingredient') }}
						&nbsp;<i class="fas fa-question-circle text-muted"
							data-toggle="tooltip"
							title="{{ $__t('This will be used as the default setting when adding this product as a recipe ingredient') }}"></i>
					</label>
				</div>
			</div>
			@else
			<input type="hidden"
				name="not_check_stock_fulfillment_for_recipes"
				id="not_check_stock_fulfillment_for_recipes"
				value="0">
			@endif

			@php if($mode == 'edit') { $value = $product->calories; } else { $value = 0; } @endphp
			@include('components.numberpicker', array(
			'id' => 'calories',
			'label' => 'Energy (kcal)',
			'min' => '0.' . str_repeat('0', $userSettings['stock_decimal_places_amounts']),
			'decimals' => $userSettings['stock_decimal_places_amounts'],
			'value' => $value,
			'hint' => $__t('Per stock quantity unit'),
			'isRequired' => false,
			'additionalCssClasses' => 'locale-number-input locale-number-quantity-amount'
			))

			@if(GROCY_FEATURE_FLAG_STOCK_PRODUCT_FREEZING)
			@php if($mode == 'edit') { $value = $product->default_best_before_days_after_freezing; } else { $value = 0; } @endphp
			@include('components.numberpicker', array(
			'id' => 'default_best_before_days_after_freezing',
			'label' => 'Default due days after freezing',
			'min' => -1,
			'value' => $value,
			'hint' => $__t('On moving this product to a freezer location (so when freezing it), the due date will be replaced by today + this amount of days') . ' (' . $__t('-1 means that this product will be never overdue') . ')'
			))

			@php if($mode == 'edit') { $value = $product->default_best_before_days_after_thawing; } else { $value = 0; } @endphp
			@include('components.numberpicker', array(
			'id' => 'default_best_before_days_after_thawing',
			'label' => 'Default due days after thawing',
			'min' => 0,
			'value' => $value,
			'hint' => $__t('On moving this product from a freezer location (so when thawing it), the due date will be replaced by today + this amount of days')
			))
			@else
			<input type="hidden"
				name="default_best_before_days_after_freezing"
				value="0">
			<input type="hidden"
				name="default_best_before_days_after_thawing"
				value="0">
			@endif

			@php if($mode == 'edit') { $value = $product->quick_consume_amount; } else { $value = 1; } @endphp
			@include('components.numberpicker', array(
			'id' => 'quick_consume_amount',
			'label' => 'Quick consume amount',
			'min' => $DEFAULT_MIN_AMOUNT,
			'decimals' => $userSettings['stock_decimal_places_amounts'],
			'value' => $value,
			'hint' => $__t('This amount is used for the "quick consume/open buttons" on the stock overview page (related to quantity unit stock)'),
			'contextInfoId' => 'quick_consume_qu_info',
			'additionalCssClasses' => 'locale-number-input locale-number-quantity-amount'
			))

			@include('components.userfieldsform', array(
			'userfields' => $userfields,
			'entity' => 'products'
			))

			@if(GROCY_FEATURE_FLAG_LABELPRINTER)
			<div class="form-group">
				<div class="custom-control custom-checkbox">
					<input @if($mode=='edit'
						&&
						$product->allow_label_per_unit == 1) checked @endif class="form-check-input custom-control-input" type="checkbox" id="allow_label_per_unit" name="allow_label_per_unit" value="1">
					<label class="form-check-label custom-control-label"
						for="allow_label_per_unit">{{ $__t('Allow label printing per unit') }}&nbsp;<i class="fas fa-question-circle text-muted"
							data-toggle="tooltip"
							title="{{ $__t('Allow printing of one label per unit on purchase (after conversion) - e.g. 1 purchased pack adding 10 pieces of stock would print 10 labels') }}"></i>
					</label>
				</div>
			</div>

			@php
			$no_label = "";
			$single_label = "";
			$per_unit_label = "";
			$disable_per_unit = "";

			if($mode == 'edit') {
			switch($product->default_print_stock_label) {
			case 0: $no_label = "selected"; break;
			case 1: $single_label = "selected"; break;
			case 2: $per_unit_label = "selected"; break;
			default: break; // yolo
			}
			if($product->allow_label_per_unit == 0) {
			$disable_per_unit="disabled";
			$per_unit_label = "";
			}
			}
			@endphp

			<div class="form-group">
				<label for="default_print_stock_label">{{ $__t('Stock entry label') }}</label>
				<select class="form-control"
					id="default_print_stock_label"
					name="default_print_stock_label">
					<option value="0"
						{{ $no_label }}>{{ $__t('No label') }}</option>
					<option value="1"
						{{ $single_label }}>{{ $__t('Single label') }}</option>
					<option value="2"
						{{ $per_unit_label }}
						{{ $disable_per_unit }}
						id="label-option-per-unit">{{ $__t('Label per unit') }}</option>
				</select>
			</div>
			@endif

			<div class="form-group">
				<div class="custom-control custom-checkbox">
					<input @if($mode=='edit'
						&&
						$product->hide_on_stock_overview == 1) checked @endif class="form-check-input custom-control-input" type="checkbox" id="hide_on_stock_overview" name="hide_on_stock_overview" value="1">
					<label class="form-check-label custom-control-label"
						for="hide_on_stock_overview">{{ $__t('Never show on stock overview') }}&nbsp;<i class="fas fa-question-circle text-muted"
							data-toggle="tooltip"
							title="{{ $__t('The stock overview page lists all products which are currently in-stock or below their min. stock amount - enable this to hide this product there always') }}"></i>
					</label>
				</div>
			</div>

			<small id="save-hint"
				class="my-2 form-text text-muted @if($mode == 'edit') d-none @endif">{{ $__t('Save & continue to add quantity unit conversions & barcodes') }}</small>

			<button id="save-product-button"
				class="save-product-button btn btn-success mb-2"
				data-location="continue">{{ $__t('Save & continue') }}</button>
			<button class="save-product-button btn btn-info mb-2"
				data-location="return">{{ $__t('Save & return to products') }}</button>
		</form>

	</div>

	<div class="col-lg-6 col-12 @if($mode == 'create') d-none @endif">

		<div class="row @if(!GROCY_FEATURE_FLAG_STOCK) d-none @endif">
			<div class="col">
				<div class="title-related-links">
					<h4>
						{{ $__t('Barcodes') }}
					</h4>
					<button class="btn btn-outline-dark d-md-none mt-2 float-right order-1 order-md-3"
						type="button"
						data-toggle="collapse"
						data-target="#related-links">
						<i class="fas fa-ellipsis-v"></i>
					</button>
					@if($mode == "edit")
					<div class="related-links collapse d-md-flex order-2 width-xs-sm-100"
						id="related-links">
						<a class="btn btn-outline-primary btn-sm m-1 mt-md-0 mb-md-0 float-right show-as-dialog-link"
							href="{{ $U('/productbarcodes/new?embedded&product=' . $product->id ) }}">
							{{ $__t('Add') }}
						</a>
					</div>
					@endif
				</div>

				<h5 id="barcode-headline-info"
					class="text-muted font-italic"></h5>

				<table id="barcode-table"
					class="table table-sm table-striped nowrap w-100">
					<thead>
						<tr>
							<th class="border-right"><a class="text-muted change-table-columns-visibility-button"
									data-toggle="tooltip"
									data-toggle="tooltip"
									title="{{ $__t('Table options') }}"
									data-table-selector="#barcode-table"
									href="#"><i class="fas fa-eye"></i></a>
							</th>
							<th>{{ $__t('Barcode') }}</th>
							<th class="@if(!GROCY_FEATURE_FLAG_STOCK_PRICE_TRACKING) d-none @endif">{{ $__t('Store') }}</th>
							<th>{{ $__t('Quantity unit') }}</th>
							<th>{{ $__t('Amount') }}</th>
							<th class="@if(!GROCY_FEATURE_FLAG_STOCK_PRICE_TRACKING) d-none @endif">{{ $__t('Last price') }}</th>
							<th>{{ $__t('Note') }}</th>

							@include('components.userfields_thead', array(
							'userfields' => $productBarcodeUserfields
							))
						</tr>
					</thead>
					<tbody class="d-none">
						@if($mode == "edit")
						@foreach($barcodes as $barcode)
						@if($barcode->product_id == $product->id || $barcode->product_id == null)
						<tr>
							<td class="fit-content border-right">
								<a class="btn btn-sm btn-info show-as-dialog-link @if($barcode->product_id == null) disabled @endif"
									href="{{ $U('/productbarcodes/' . $barcode->id . '?embedded&product=' . $product->id ) }}">
									<i class="fas fa-edit"></i>
								</a>
								<a class="btn btn-sm btn-danger barcode-delete-button @if($barcode->product_id == null) disabled @endif"
									href="#"
									data-barcode-id="{{ $barcode->id }}"
									data-barcode="{{ $barcode->barcode }}"
									data-product-barcode="{{ $product->barcode }}"
									data-product-id="{{ $product->id }}">
									<i class="fas fa-trash"></i>
								</a>
							</td>
							<td>
								{{ $barcode->barcode }}
							</td>
							<td class="@if(!GROCY_FEATURE_FLAG_STOCK_PRICE_TRACKING) d-none @endif"
								id="barcode-shopping-location">
								@if (FindObjectInArrayByPropertyValue($shoppinglocations, 'id', $barcode->shopping_location_id) !== null)
								{{ FindObjectInArrayByPropertyValue($shoppinglocations, 'id', $barcode->shopping_location_id)->name }}
								@endif
							</td>
							<td>
								@if(!empty($barcode->qu_id))
								{{ FindObjectInArrayByPropertyValue($quantityunits, 'id', $barcode->qu_id)->name }}
								@endif
							</td>
							<td>
								@if(!empty($barcode->amount))
								<span class="locale-number locale-number-quantity-amount">{{ $barcode->amount }}</span>
								@endif
							</td>
							<td class="@if(!GROCY_FEATURE_FLAG_STOCK_PRICE_TRACKING) d-none @endif">
								<span class="locale-number locale-number-currency">{{ $barcode->last_price }}</span>
							</td>
							<td>
								{{ $barcode->note }}
							</td>

							@include('components.userfields_tbody', array(
							'userfields' => $productBarcodeUserfields,
							'userfieldValues' => FindAllObjectsInArrayByPropertyValue($productBarcodeUserfieldValues, 'object_id', $barcode->id)
							))
						</tr>
						@endif
						@endforeach
						@endif
					</tbody>
				</table>
			</div>
		</div>

		@if($mode == 'edit')
		<div class="row mt-2">
			<div class="col clearfix">
				<div class="title-related-links">
					<h4>
						{{ $__t('grocycode') }}
						<i class="fas fa-question-circle text-muted"
							data-toggle="tooltip"
							title="{{ $__t('grocycode is a unique referer to this product in your grocy instance - print it onto a label and scan it like any other barcode') }}"></i>
					</h4>
					<p>
						<img src="{{ $U('/product/' . $product->id . '/grocycode?size=60') }}"
							class="float-lg-left">
					</p>
					<p>
						<a class="btn btn-outline-primary btn-sm"
							href="{{ $U('/product/' . $product->id . '/grocycode?download=true') }}">{{ $__t('Download') }}</a>
						@if(GROCY_FEATURE_FLAG_LABELPRINTER)
						<a class="btn btn-outline-primary btn-sm stockentry-grocycode-product-label-print"
							data-product-id="{{ $product->id }}"
							href="#">
							{{ $__t('Print on label printer') }}
						</a>
						@endif
					</p>
				</div>
			</div>
		</div>
		@endif

		<div class="row @if(GROCY_FEATURE_FLAG_STOCK) mt-5 @endif">
			<div class="col">
				<div class="title-related-links">
					<h4>
						{{ $__t('QU conversions') }}
					</h4>
					<button class="btn btn-outline-dark d-md-none mt-2 float-right order-1 order-md-3"
						type="button"
						data-toggle="collapse"
						data-target="#related-links">
						<i class="fas fa-ellipsis-v"></i>
					</button>
					@if($mode == "edit")
					<div class="related-links collapse d-md-flex order-2 width-xs-sm-100"
						id="related-links">
						<a class="btn btn-outline-primary btn-sm m-1 mt-md-0 mb-md-0 float-right show-as-dialog-link"
							href="{{ $U('/quantityunitconversion/new?embedded&product=' . $product->id ) }}">
							{{ $__t('Add') }}
						</a>
					</div>
					@endif
				</div>

				<h5 id="qu-conversion-headline-info"
					class="text-muted font-italic"></h5>

				<table id="qu-conversions-table-products"
					class="table table-sm table-striped nowrap w-100">
					<thead>
						<tr>
							<th class="border-right"><a class="text-muted change-table-columns-visibility-button"
									data-toggle="tooltip"
									title="{{ $__t('Table options') }}"
									data-table-selector="#qu-conversions-table-products"
									href="#"><i class="fas fa-eye"></i></a>
							</th>
							<th>{{ $__t('Quantity unit from') }}</th>
							<th>{{ $__t('Quantity unit to') }}</th>
							<th>{{ $__t('Factor') }}</th>
							<th>{{ $__t('Group')}}</th>
							<th></th>
						</tr>
					</thead>
					<tbody class="d-none">
						@if($mode == "edit")
						@foreach($quConversions as $quConversion)
						@if($quConversion->product_id == $product->id || $quConversion->product_id == null && ($quConversion->product_id != null || $quConversion->from_qu_id == $product->qu_id_purchase || $quConversion->from_qu_id == $product->qu_id_stock || $quConversion->to_qu_id == $product->qu_id_purchase || $quConversion->to_qu_id == $product->qu_id_stock))
						<tr>
							<td class="fit-content border-right">
								<a class="btn btn-sm btn-info show-as-dialog-link @if($quConversion->product_id == null) disabled @endif"
									href="{{ $U('/quantityunitconversion/' . $quConversion->id . '?embedded&product=' . $product->id ) }}">
									<i class="fas fa-edit"></i>
								</a>
								<a class="btn btn-sm btn-danger qu-conversion-delete-button @if($quConversion->product_id == null) disabled @endif"
									href="#"
									data-qu-conversion-id="{{ $quConversion->id }}">
									<i class="fas fa-trash"></i>
								</a>
							</td>
							<td>
								{{ FindObjectInArrayByPropertyValue($quantityunits, 'id', $quConversion->from_qu_id)->name }}
							</td>
							<td>
								{{ FindObjectInArrayByPropertyValue($quantityunits, 'id', $quConversion->to_qu_id)->name }}
							</td>
							<td>
								<span class="locale-number locale-number-quantity-amount">{{ $quConversion->factor }}</span>
							</td>
							<td class="d-none">
								@if($quConversion->product_id != null)
								{{ $__t('Product overrides') }}
								@else
								{{ $__t('Default conversions') }}
								@endif
							</td>
							<td class="font-italic">
								{!! $__t('This means 1 %1$s is the same as %2$s %3$s', FindObjectInArrayByPropertyValue($quantityunits, 'id', $quConversion->from_qu_id)->name, '<span class="locale-number locale-number-quantity-amount">' . $quConversion->factor . '</span>', $__n($quConversion->factor, FindObjectInArrayByPropertyValue($quantityunits, 'id', $quConversion->to_qu_id)->name, FindObjectInArrayByPropertyValue($quantityunits, 'id', $quConversion->to_qu_id)->name_plural)) !!}
							</td>
						</tr>
						@endif
						@endforeach
						@endif
					</tbody>
				</table>
			</div>
		</div>

		<div class="row mt-5">
			<div class="col">
				<div class="title-related-links">
					<h4>
						{{ $__t('Picture') }}
					</h4>
					<div class="form-group w-75 m-0">
						<div class="input-group">
							<div class="custom-file">
								<input type="file"
									class="custom-file-input"
									id="product-picture"
									accept="image/*">
								@if($mode == "edit")
								<label id="product-picture-label"
									class="custom-file-label @if(empty($product->picture_file_name)) d-none @endif"
									for="product-picture">
									{{ $product->picture_file_name }}
								</label>
								<label id="product-picture-label-none"
									class="custom-file-label @if(!empty($product->picture_file_name)) d-none @endif"
									for="product-picture">
									{{ $__t('No file selected') }}
								</label>
								@endif
							</div>
							<div class="input-group-append">
								<span class="input-group-text"><i class="fas fa-trash"
										id="delete-current-product-picture-button"></i></span>
							</div>
						</div>
					</div>
				</div>
				@if($mode == "edit" && !empty($product->picture_file_name))
				<img id="current-product-picture"
					data-src="{{ $U('/api/files/productpictures/' . base64_encode($product->picture_file_name) . '?force_serve_as=picture&best_fit_width=400') }}"
					class="img-fluid img-thumbnail mt-2 lazy mb-5">
				<p id="delete-current-product-picture-on-save-hint"
					class="form-text text-muted font-italic d-none mb-5">{{ $__t('The current picture will be deleted on save') }}</p>
				@else
				<p id="no-current-product-picture-hint"
					class="form-text text-muted font-italic mb-5">{{ $__t('No picture available') }}</p>
				@endif
			</div>
		</div>
	</div>
</div>
@stop
