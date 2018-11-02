@extends('layout.default')

@if($mode == 'edit')
	@section('title', $L('Edit recipe'))
@else
	@section('title', $L('Create recipe'))
@endif

@section('viewJsName', 'recipeform')

@push('pageScripts')
	<script src="{{ $U('/node_modules/summernote/dist/summernote-bs4.js?v=', true) }}{{ $version }}"></script>
	@if(!empty($L('summernote_locale')))<script src="{{ $U('/node_modules', true) }}/summernote/dist/lang/summernote-{{ $L('summernote_locale') }}.js?v={{ $version }}"></script>@endif
	<script src="{{ $U('/node_modules/datatables.net-rowgroup/js/dataTables.rowGroup.min.js?v=', true) }}{{ $version }}"></script>
	<script src="{{ $U('/node_modules/datatables.net-rowgroup-bs4/js/rowGroup.bootstrap4.min.js?v=', true) }}{{ $version }}"></script>
@endpush

@push('pageStyles')
	<link href="{{ $U('/node_modules/summernote/dist/summernote-bs4.css?v=', true) }}{{ $version }}" rel="stylesheet">
	<link href="{{ $U('/node_modules/datatables.net-rowgroup-bs4/css/rowGroup.bootstrap4.min.css?v=', true) }}{{ $version }}" rel="stylesheet">
@endpush

@section('content')
<div class="row">
	<div class="col">
		<h1>@yield('title')</h1>

		<script>Grocy.EditMode = '{{ $mode }}';</script>

		@if($mode == 'edit')
			<script>Grocy.EditObjectId = {{ $recipe->id }};</script>
		@endif
	</div>
</div>

<div class="row">
	<div class="col-xs-12 col-md-7 pb-3">
		<form id="recipe-form" novalidate>

			<div class="form-group">
				<label for="name">{{ $L('Name') }}</label>
				<input type="text" class="form-control" required id="name" name="name" value="@if($mode == 'edit'){{ $recipe->name }}@endif">
				<div class="invalid-feedback">{{ $L('A name is required') }}</div>
			</div>

			<div class="form-group">
				<label for="description">{{ $L('Preparation') }}</label>
				<textarea id="description" class="form-control" name="description">@if($mode == 'edit'){{ $recipe->description }}@endif</textarea>
			</div>

			<button id="save-recipe-button" class="btn btn-success">{{ $L('Save') }}</button>

		</form>
	</div>

	<div class="col-xs-12 col-md-5 pb-3">
		<div class="row">
			<div class="col">
				<h2>
					{{ $L('Ingredients list') }}
					<a id="recipe-pos-add-button" class="btn btn-outline-dark" href="#">
						<i class="fas fa-plus"></i> {{ $L('Add') }}
					</a>
				</h2>
				<table id="recipes-pos-table" class="table table-sm table-striped dt-responsive">
					<thead>
						<tr>
							<th>#</th>
							<th>{{ $L('Product') }}</th>
							<th>{{ $L('Amount') }}</th>
							<th>{{ $L('Note') }}</th>
							<th class="d-none">Hiden ingredient group</th>
						</tr>
					</thead>
					<tbody>
						@if($mode == "edit")
						@foreach($recipePositions as $recipePosition)
						<tr class="@if(FindObjectInArrayByPropertyValue($recipesFulfillment, 'recipe_pos_id', $recipePosition->id)->need_fulfilled == 1) table-success @elseif(FindObjectInArrayByPropertyValue($recipesFulfillment, 'recipe_pos_id', $recipePosition->id)->need_fulfilled_with_shopping_list == 1) table-warning  @else table-danger @endif">
							<td class="fit-content">
								<a class="btn btn-sm btn-info recipe-pos-edit-button" href="#" data-recipe-pos-id="{{ $recipePosition->id }}">
									<i class="fas fa-edit"></i>
								</a>
								<a class="btn btn-sm btn-danger recipe-pos-delete-button" href="#" data-recipe-pos-id="{{ $recipePosition->id }}" data-recipe-pos-name="{{ FindObjectInArrayByPropertyValue($products, 'id', $recipePosition->product_id)->name }}">
									<i class="fas fa-trash"></i>
								</a>
								<a class="btn btn-sm btn-primary recipe-pos-order-missing-button @if(FindObjectInArrayByPropertyValue($recipesFulfillment, 'recipe_pos_id', $recipePosition->id)->need_fulfilled_with_shopping_list == 1){{ disabled }}@endif" href="#" data-toggle="tooltip" data-placement="right" title="{{ $L('Put missing amount on shopping list') }}" data-recipe-name="{{ $recipe->name }}" data-product-id="{{ $recipePosition->product_id }}" data-product-amount="{{ round(FindObjectInArrayByPropertyValue($recipesFulfillment, 'recipe_pos_id', $recipePosition->id)->missing_amount) }}" data-product-name="{{ FindObjectInArrayByPropertyValue($products, 'id', $recipePosition->product_id)->name }}">
									<i class="fas fa-cart-plus"></i>
								</a>
							</td>
							<td>
								{{ FindObjectInArrayByPropertyValue($products, 'id', $recipePosition->product_id)->name }}
							</td>
							<td>
								@if($recipePosition->amount == round($recipePosition->amount)){{ round($recipePosition->amount) }}@else{{ $recipePosition->amount }}@endif {{ Pluralize($recipePosition->amount, FindObjectInArrayByPropertyValue($quantityunits, 'id', $recipePosition->qu_id)->name, FindObjectInArrayByPropertyValue($quantityunits, 'id', $recipePosition->qu_id)->name_plural) }}
								<span class="timeago-contextual">@if(FindObjectInArrayByPropertyValue($recipesFulfillment, 'recipe_pos_id', $recipePosition->id)->need_fulfilled == 1) {{ $L('Enough in stock') }} @else {{ $L('Not enough in stock, #1 missing, #2 already on shopping list', round(FindObjectInArrayByPropertyValue($recipesFulfillment, 'recipe_pos_id', $recipePosition->id)->missing_amount), round(FindObjectInArrayByPropertyValue($recipesFulfillment, 'recipe_pos_id', $recipePosition->id)->amount_on_shopping_list)) }} @endif</span>
							</td>
							<td class="fit-content">
								<a class="btn btn-sm btn-info recipe-pos-show-note-button @if(empty($recipePosition->note)) disabled @endif" href="#" data-toggle="tooltip" data-placement="top" title="{{ $L('Show notes') }}" data-recipe-pos-note="{{ $recipePosition->note }}">
									<i class="fas fa-eye"></i>
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
				<h2>
					{{ $L('Included recipes') }}
					<a id="recipe-include-add-button" class="btn btn-outline-dark" href="#">
						<i class="fas fa-plus"></i> {{ $L('Add') }}
					</a>
				</h2>
				<table id="recipes-includes-table" class="table table-sm table-striped dt-responsive">
					<thead>
						<tr>
							<th>#</th>
							<th>{{ $L('Recipe') }}</th>
						</tr>
					</thead>
					<tbody>
						@if($mode == "edit")
						@foreach($recipeNestings as $recipeNesting)
						<tr>
							<td class="fit-content">
								<a class="btn btn-sm btn-info recipe-include-edit-button" href="#" data-recipe-include-id="{{ $recipeNesting->id }}" data-recipe-included-recipe-id="{{ $recipeNesting->includes_recipe_id }}">
									<i class="fas fa-edit"></i>
								</a>
								<a class="btn btn-sm btn-danger recipe-include-delete-button" href="#" data-recipe-include-id="{{ $recipeNesting->id }}" data-recipe-include-name="{{ FindObjectInArrayByPropertyValue($recipes, 'id', $recipeNesting->includes_recipe_id)->name }}">
									<i class="fas fa-trash"></i>
								</a>
							</td>
							<td>
								{{ FindObjectInArrayByPropertyValue($recipes, 'id', $recipeNesting->includes_recipe_id)->name }}
							</td>
						</tr>
						@endforeach
						@endif
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="recipe-include-editform-modal" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content text-center">
			<div class="modal-header">
				<h4 id="recipe-include-editform-title" class="modal-title w-100"></h4>
			</div>
			<div class="modal-body">
				<form id="recipe-include-form" novalidate>
					<div class="form-group">
						<label for="includes_recipe_id">{{ $L('Recipe') }}</label>
						<select required class="form-control" id="includes_recipe_id" name="includes_recipe_id">
							<option></option>
							@foreach($recipes as $recipeForList)
								@if($recipeForList->id !== $recipe->id)
									<option data-already-included="{{ BoolToString(FindObjectInArrayByPropertyValue($recipeNestings, 'includes_recipe_id', $recipeForList->id) === null) }}" value="{{ $recipeForList->id }}">{{ $recipeForList->name }}</option>
								@endif
							@endforeach
						</select>
						<div class="invalid-feedback">{{ $L('A recipe is required') }}</div>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">{{ $L('Cancel') }}</button>
				<button id="save-recipe-include-button" data-dismiss="modal" class="btn btn-success">{{ $L('Save') }}</button>
			</div>
		</div>
	</div>
</div>
@stop
