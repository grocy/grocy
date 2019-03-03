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

			@if(!empty($recipe->picture_file_name))
				<script>Grocy.RecipePictureFileName = '{{ $recipe->picture_file_name }}';</script>
			@endif
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

			@php if($mode == 'edit') { $value = $recipe->base_servings; } else { $value = 1; } @endphp
			@include('components.numberpicker', array(
				'id' => 'base_servings',
				'label' => 'Servings',
				'min' => 1,
				'value' => $value,
				'invalidFeedback' => $L('This cannot be lower than #1', '1'),
				'hint' => $L('The ingredients listed here result in this amount of servings')
			))

			<div class="form-group">
				<div class="form-check">
					<input type="hidden" name="not_check_shoppinglist" value="0">
					<input @if($mode == 'edit' && $recipe->not_check_shoppinglist == 1) checked @endif class="form-check-input" type="checkbox" id="not_check_shoppinglist" name="not_check_shoppinglist" value="1">
					<label class="form-check-label" for="not_check_shoppinglist">{{ $L('Do not check against the shopping list when adding missing items to it') }}&nbsp;&nbsp;
						<span class="small text-muted">{{ $L('By default the amount to be added to the shopping list is "needed amount - stock amount - shopping list amount" - when this is enabled, it is only checked against the stock amount, not against what is already on the shopping list') }}</span>
					</label>
				</div>
			</div>

			<div class="form-group">
				<label for="recipe-picture">{{ $L('Picture') }}</label>
				<div class="custom-file">
					<input type="file" class="custom-file-input" id="recipe-picture" accept="image/*">
					<label class="custom-file-label" for="recipe-picture">{{ $L('No file selected') }}</label>
				</div>
				<p class="form-text text-muted small">{{ $L('If you don\'t select a file, the current picture will not be altered') }}</p>
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
							<th class="fit-content">{{ $L('Note') }}</th>
							<th class="d-none">Hiden ingredient group</th>
						</tr>
					</thead>
					<tbody class="d-none">
						@if($mode == "edit")
						@foreach($recipePositions as $recipePosition)
						<tr>
							<td class="fit-content">
								<a class="btn btn-sm btn-info recipe-pos-edit-button" href="#" data-recipe-pos-id="{{ $recipePosition->id }}">
									<i class="fas fa-edit"></i>
								</a>
								<a class="btn btn-sm btn-danger recipe-pos-delete-button" href="#" data-recipe-pos-id="{{ $recipePosition->id }}" data-recipe-pos-name="{{ FindObjectInArrayByPropertyValue($products, 'id', $recipePosition->product_id)->name }}">
									<i class="fas fa-trash"></i>
								</a>
							</td>
							<td>
								{{ FindObjectInArrayByPropertyValue($products, 'id', $recipePosition->product_id)->name }}
							</td>
							<td>
								@if($recipePosition->amount == round($recipePosition->amount)){{ round($recipePosition->amount) }}@else{{ $recipePosition->amount }}@endif {{ Pluralize($recipePosition->amount, FindObjectInArrayByPropertyValue($quantityunits, 'id', $recipePosition->qu_id)->name, FindObjectInArrayByPropertyValue($quantityunits, 'id', $recipePosition->qu_id)->name_plural) }}
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
					<tbody class="d-none">
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

		<div class="row mt-5">
			<div class="col">
				<label class="mt-2">{{ $L('Current picture') }}</label>
				<button id="delete-current-recipe-picture-button" class="btn btn-sm btn-danger @if(empty($recipe->picture_file_name)) disabled @endif"><i class="fas fa-trash"></i> {{ $L('Delete') }}</button>
				@if(!empty($recipe->picture_file_name))
					<p><img id="current-recipe-picture" src="{{ $U('/api/files/recipepictures/' . base64_encode($recipe->picture_file_name)) }}" class="img-fluid img-thumbnail mt-2"></p>
					<p id="delete-current-recipe-picture-on-save-hint" class="form-text text-muted font-italic d-none">{{ $L('The current picture will be deleted when you save the recipe') }}</p>
				@else
					<p id="no-current-recipe-picture-hint" class="form-text text-muted font-italic">{{ $L('No picture available') }}</p>
				@endif
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
