@php require_frontend_packages(['datatables', 'summernote']); @endphp

@extends('layout.default')

@if($mode == 'edit')
@section('title', $__t('Edit recipe'))
@else
@section('title', $__t('Create recipe'))
@endif

@section('content')
<div class="row">
	<div class="col">
		<h2 class="title">@yield('title')</h2>

		<script>
			Grocy.EditMode = '{{ $mode }}';
			Grocy.QuantityUnits = {!! json_encode($quantityunits) !!};
			Grocy.QuantityUnitConversionsResolved = {!! json_encode($quantityUnitConversionsResolved) !!};
		</script>

		@if($mode == 'edit')
		<script>
			Grocy.EditObjectId = {{ $recipe->id }};
		</script>

		@if(!empty($recipe->picture_file_name))
		<script>
			Grocy.RecipePictureFileName = '{{ $recipe->picture_file_name }}';
		</script>
		@endif
		@endif
	</div>
</div>

<hr class="my-2">

<div class="row">
	<div class="col-12 col-md-7 pb-3">
		<form id="recipe-form"
			novalidate>

			<div class="form-group">
				<label for="name">{{ $__t('Name') }}</label>
				<input type="text"
					class="form-control"
					required
					id="name"
					name="name"
					value="@if($mode == 'edit'){{ $recipe->name }}@endif">
				<div class="invalid-feedback">{{ $__t('A name is required') }}</div>
			</div>

			@php if($mode == 'edit') { $value = $recipe->base_servings; } else { $value = 1; } @endphp
			@include('components.numberpicker', array(
			'id' => 'base_servings',
			'label' => 'Servings',
			'min' => $DEFAULT_MIN_AMOUNT,
			'decimals' => $userSettings['stock_decimal_places_amounts'],
			'value' => $value,
			'hint' => $__t('The ingredients listed here result in this amount of servings'),
			'additionalCssClasses' => 'locale-number-input locale-number-quantity-amount'
			))

			<div class="form-group">
				<div class="custom-control custom-checkbox">
					<input @if($mode=='edit'
						&&
						$recipe->not_check_shoppinglist == 1) checked @endif class="form-check-input custom-control-input" type="checkbox" id="not_check_shoppinglist" name="not_check_shoppinglist" value="1">
					<label class="form-check-label custom-control-label"
						for="not_check_shoppinglist">
						{{ $__t('Do not check against the shopping list when adding missing items to it') }}&nbsp;
						<i class="fa-solid fa-question-circle text-muted"
							data-toggle="tooltip"
							data-trigger="hover click"
							title="{{ $__t('By default the amount to be added to the shopping list is "needed amount - stock amount - shopping list amount" - when this is enabled, it is only checked against the stock amount, not against what is already on the shopping list') }}"></i>
					</label>
				</div>
			</div>

			@include('components.productpicker', array(
			'products' => $products,
			'isRequired' => false,
			'label' => 'Produces product',
			'prefillById' => $mode == 'edit' ? $recipe->product_id : '',
			'hint' => $__t('When a product is selected, one unit (per serving in stock quantity unit) will be added to stock on consuming this recipe'),
			'disallowAllProductWorkflows' => true,
			))

			@include('components.userfieldsform', array(
			'userfields' => $userfields,
			'entity' => 'recipes'
			))

			<div class="form-group">
				<label for="description">{{ $__t('Preparation') }}</label>
				<textarea id="description"
					class="form-control wysiwyg-editor"
					name="description">@if($mode == 'edit'){{ $recipe->description }}@endif</textarea>
			</div>

			<small class="my-2 form-text text-muted @if($mode == 'edit') d-none @endif">{{ $__t('Save & continue to add ingredients and included recipes') }}</small>

			<button class="save-recipe btn btn-success mb-2"
				data-location="continue">{{ $__t('Save & continue') }}</button>
			<button class="save-recipe btn btn-info mb-2"
				data-location="return">{{ $__t('Save & return to recipes') }}</button>

		</form>
	</div>

	<div class="col-12 col-md-5 pb-3 @if($mode == 'create') d-none @endif">
		<div class="row">
			<div class="col">
				<div class="title-related-links">
					<h4>
						{{ $__t('Ingredients list') }}
					</h4>
					<button class="btn btn-outline-dark d-md-none mt-2 float-right order-1 order-md-3"
						type="button"
						data-toggle="collapse"
						data-target="#related-links">
						<i class="fa-solid fa-ellipsis-v"></i>
					</button>
					<div class="related-links collapse d-md-flex order-2 width-xs-sm-100"
						id="related-links">
						<a id="recipe-pos-add-button"
							class="btn btn-outline-primary btn-sm recipe-pos-add-button m-1 mt-md-0 mb-md-0 float-right"
							type="button"
							href="#">
							{{ $__t('Add') }}
						</a>
					</div>
				</div>

				<table id="recipes-pos-table"
					class="table table-sm table-striped nowrap w-100">
					<thead>
						<tr>
							<th class="border-right"><a class="text-muted change-table-columns-visibility-button"
									data-toggle="tooltip"
									title="{{ $__t('Table options') }}"
									data-table-selector="#recipes-pos-table"
									href="#"><i class="fa-solid fa-eye"></i></a>
							</th>
							<th>{{ $__t('Product') }}</th>
							<th>{{ $__t('Amount') }}</th>
							<th class="fit-content">{{ $__t('Note') }}</th>
							<th class="allow-grouping">{{ $__t('Ingredient group') }}</th>
						</tr>
					</thead>
					<tbody class="d-none">
						@if($mode == "edit")
						@foreach($recipePositions as $recipePosition)
						<tr>
							<td class="fit-content border-right">
								<a class="btn btn-sm btn-info recipe-pos-edit-button"
									type="button"
									href="#"
									data-recipe-pos-id="{{ $recipePosition->id }}"
									data-product-id="{{ $recipePosition->product_id }}">
									<i class="fa-solid fa-edit"></i>
								</a>
								<a class="btn btn-sm btn-danger recipe-pos-delete-button"
									href="#"
									data-recipe-pos-id="{{ $recipePosition->id }}"
									data-recipe-pos-name="{{ FindObjectInArrayByPropertyValue($products, 'id', $recipePosition->product_id)->name }}">
									<i class="fa-solid fa-trash"></i>
								</a>
							</td>
							<td>
								{{ FindObjectInArrayByPropertyValue($products, 'id', $recipePosition->product_id)->name }}
							</td>
							<td>
								@php
								// The amount can't be non-numeric when using the frontend,
								// but some users decide to edit the database manually and
								// enter something like "4 or 5" in the amount column (brilliant)
								// => So at least don't crash this view by just assuming 0 if that's the case
								if (!is_numeric($recipePosition->amount))
								{
								$recipePosition->amount = 0;
								}

								$product = FindObjectInArrayByPropertyValue($products, 'id', $recipePosition->product_id);
								$productQuConversions = FindAllObjectsInArrayByPropertyValue($quantityUnitConversionsResolved, 'product_id', $product->id);
								$productQuConversions = FindAllObjectsInArrayByPropertyValue($productQuConversions, 'from_qu_id', $product->qu_id_stock);
								$productQuConversion = FindObjectInArrayByPropertyValue($productQuConversions, 'to_qu_id', $recipePosition->qu_id);
								if ($productQuConversion && $recipePosition->only_check_single_unit_in_stock == 0)
								{
								$recipePosition->amount = $recipePosition->amount * $productQuConversion->factor;
								}
								@endphp
								@if(!empty($recipePosition->variable_amount))
								{{ $recipePosition->variable_amount }}
								@else
								<span class="locale-number locale-number-quantity-amount">@if($recipePosition->amount == round($recipePosition->amount)){{ round($recipePosition->amount) }}@else{{ $recipePosition->amount }}@endif</span>
								@endif
								{{ $__n($recipePosition->amount, FindObjectInArrayByPropertyValue($quantityunits, 'id', $recipePosition->qu_id)->name, FindObjectInArrayByPropertyValue($quantityunits, 'id', $recipePosition->qu_id)->name_plural, true) }}

								@if(!empty($recipePosition->variable_amount))
								<div class="small text-muted font-italic">{{ $__t('Variable amount') }}</div>
								@endif
							</td>
							<td class="fit-content">
								<a class="btn btn-sm btn-info recipe-pos-show-note-button @if(empty($recipePosition->note)) disabled @endif"
									href="#"
									data-toggle="tooltip"
									data-placement="top"
									title="{{ $__t('Show notes') }}"
									data-recipe-pos-note="{{ $recipePosition->note }}">
									<i class="fa-solid fa-eye"></i>
								</a>
							</td>
							<td>
								{{ $recipePosition->ingredient_group }}
							</td>
						</tr>
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
						{{ $__t('Included recipes') }}
					</h4>
					<button class="btn btn-outline-dark d-md-none mt-2 float-right order-1 order-md-3"
						type="button"
						data-toggle="collapse"
						data-target="#related-links">
						<i class="fa-solid fa-ellipsis-v"></i>
					</button>
					<div class="related-links collapse d-md-flex order-2 width-xs-sm-100"
						id="related-links">
						<a id="recipe-include-add-button"
							class="btn btn-outline-primary btn-sm m-1 mt-md-0 mb-md-0 float-right"
							href="#">
							{{ $__t('Add') }}
						</a>
					</div>
				</div>
				<table id="recipes-includes-table"
					class="table table-sm table-striped nowrap w-100">
					<thead>
						<tr>
							<th class="border-right"><a class="text-muted change-table-columns-visibility-button"
									data-toggle="tooltip"
									title="{{ $__t('Table options') }}"
									data-table-selector="#recipes-includes-table"
									href="#"><i class="fa-solid fa-eye"></i></a>
							</th>
							<th>{{ $__t('Recipe') }}</th>
							<th>{{ $__t('Servings') }}</th>
						</tr>
					</thead>
					<tbody class="d-none">
						@if($mode == "edit")
						@foreach($recipeNestings as $recipeNesting)
						<tr>
							<td class="fit-content border-right">
								<a class="btn btn-sm btn-info recipe-include-edit-button"
									href="#"
									data-recipe-include-id="{{ $recipeNesting->id }}"
									data-recipe-included-recipe-id="{{ $recipeNesting->includes_recipe_id }}"
									data-recipe-included-recipe-servings="{{ $recipeNesting->servings }}">
									<i class="fa-solid fa-edit"></i>
								</a>
								<a class="btn btn-sm btn-danger recipe-include-delete-button"
									href="#"
									data-recipe-include-id="{{ $recipeNesting->id }}"
									data-recipe-include-name="{{ FindObjectInArrayByPropertyValue($recipes, 'id', $recipeNesting->includes_recipe_id)->name }}">
									<i class="fa-solid fa-trash"></i>
								</a>
							</td>
							<td>
								{{ FindObjectInArrayByPropertyValue($recipes, 'id', $recipeNesting->includes_recipe_id)->name }}
							</td>
							<td>
								{{ $recipeNesting->servings }}
							</td>
						</tr>
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
									id="recipe-picture"
									accept="image/*">
								<label id="recipe-picture-label"
									class="custom-file-label @if(empty($recipe->picture_file_name)) d-none @endif"
									for="recipe-picture">
									{{ $recipe->picture_file_name }}
								</label>
								<label id="recipe-picture-label-none"
									class="custom-file-label @if(!empty($recipe->picture_file_name)) d-none @endif"
									for="recipe-picture">
									{{ $__t('No file selected') }}
								</label>
							</div>
							<div class="input-group-append">
								<span class="input-group-text"><i class="fa-solid fa-trash"
										id="delete-current-recipe-picture-button"></i></span>
							</div>
						</div>
					</div>
				</div>
				@if(!empty($recipe->picture_file_name))
				<img id="current-recipe-picture"
					src="{{ $U('/api/files/recipepictures/' . base64_encode($recipe->picture_file_name) . '?force_serve_as=picture&best_fit_width=400') }}"
					class="img-fluid img-thumbnail mt-2 mb-5"
					loading="lazy">
				<p id="delete-current-recipe-picture-on-save-hint"
					class="form-text text-muted font-italic d-none mb-5">{{ $__t('The current picture will be deleted on save') }}</p>
				@else
				<p id="no-current-recipe-picture-hint"
					class="form-text text-muted font-italic mb-5">{{ $__t('No picture available') }}</p>
				@endif
			</div>
		</div>

		<div class="row">
			<div class="col">
				<div class="title-related-links">
					<h4>
						<span class="ls-n1">{{ $__t('Grocycode') }}</span>
						<i class="fa-solid fa-question-circle text-muted"
							data-toggle="tooltip"
							data-trigger="hover click"
							title="{{ $__t('Grocycode is a unique referer to this %s in your Grocy instance - print it onto a label and scan it like any other barcode', $__t('Recipe')) }}"></i>
					</h4>
					<p>
						@if($mode == 'edit')
						<img src="{{ $U('/recipe/' . $recipe->id . '/grocycode?size=60') }}"
							class="float-lg-left"
							loading="lazy">
						@endif
					</p>
					<p>
						<a class="btn btn-outline-primary btn-sm"
							href="{{ $U('/recipe/' . $recipe->id . '/grocycode?download=true') }}">{{ $__t('Download') }}</a>
						@if(GROCY_FEATURE_FLAG_LABEL_PRINTER)
						<a class="btn btn-outline-primary btn-sm recipe-grocycode-label-print"
							data-recipe-id="{{ $recipe->id }}"
							href="#">
							{{ $__t('Print on label printer') }}
						</a>
						@endif
					</p>
				</div>
			</div>
		</div>
	</div>

</div>

<div class="modal fade"
	id="recipe-include-editform-modal"
	tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content text-center">
			<div class="modal-header">
				<h4 id="recipe-include-editform-title"
					class="modal-title w-100"></h4>
			</div>
			<div class="modal-body">
				<form id="recipe-include-form"
					novalidate>

					@include('components.recipepicker', array(
					'recipes' => $recipes,
					'isRequired' => true
					))

					@include('components.numberpicker', array(
					'id' => 'includes_servings',
					'label' => 'Servings',
					'min' => $DEFAULT_MIN_AMOUNT,
					'decimals' => $userSettings['stock_decimal_places_amounts'],
					'value' => '1',
					'additionalCssClasses' => 'locale-number-input locale-number-quantity-amount'
					))

				</form>
			</div>
			<div class="modal-footer">
				<button type="button"
					class="btn btn-secondary"
					data-dismiss="modal">{{ $__t('Cancel') }}</button>
				<button id="save-recipe-include-button"
					class="btn btn-success">{{ $__t('Save') }}</button>
			</div>
		</div>
	</div>
</div>
@stop
