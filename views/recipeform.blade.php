@extends('layout.default')

@if($mode == 'edit')
	@section('title', $L('Edit recipe'))
@else
	@section('title', $L('Create recipe'))
@endif

@section('viewJsName', 'recipeform')

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
				<textarea id="description" class="form-control" name="description" rows="25">@if($mode == 'edit'){{ $recipe->description }}@endif</textarea>
			</div>

			<button id="save-recipe-button" type="submit" class="btn btn-success">{{ $L('Save') }}</button>

		</form>
	</div>

	<div class="col-xs-12 col-md-5 pb-3">
		<h2>
			{{ $L('Ingredients list') }}
			<a id="recipe-pos-add-button" class="btn btn-outline-dark" href="#">
				<i class="fas fa-plus"></i> {{ $L('Add') }}
			</a>
		</h1>
		<table id="recipes-pos-table" class="table table-sm table-striped dt-responsive">
			<thead>
				<tr>
					<th>#</th>
					<th>{{ $L('Product') }}</th>
					<th>{{ $L('Amount') }}</th>
					<th>{{ $L('Note') }}</th>
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
						<a class="btn btn-sm btn-primary recipe-pos-order-missing-button @if(FindObjectInArrayByPropertyValue($recipesFulfillment, 'recipe_pos_id', $recipePosition->id)->need_fulfilled_with_shopping_list == 1){{ disabled }}@endif" href="#" data-toggle="tooltip" data-placement="right" title="{{ $L('Put missing amount on shopping list') }}" data-recipe-name="{{ $recipe->name }}" data-product-id="{{ $recipePosition->product_id }}" data-product-amount="{{ FindObjectInArrayByPropertyValue($recipesFulfillment, 'recipe_pos_id', $recipePosition->id)->missing_amount }}" data-product-name="{{ FindObjectInArrayByPropertyValue($products, 'id', $recipePosition->product_id)->name }}">
							<i class="fas fa-cart-plus"></i>
						</a>
					</td>
					<td>
						{{ FindObjectInArrayByPropertyValue($products, 'id', $recipePosition->product_id)->name }}
					</td>
					<td>
						{{ $recipePosition->amount }} {{ Pluralize($recipePosition->amount, FindObjectInArrayByPropertyValue($quantityunits, 'id', $recipePosition->qu_id)->name, FindObjectInArrayByPropertyValue($quantityunits, 'id', $recipePosition->qu_id)->name_plural) }}
						<span class="timeago-contextual">@if(FindObjectInArrayByPropertyValue($recipesFulfillment, 'recipe_pos_id', $recipePosition->id)->need_fulfilled == 1) {{ $L('Enough in stock') }} @else {{ $L('Not enough in stock, #1 missing, #2 already on shopping list', FindObjectInArrayByPropertyValue($recipesFulfillment, 'recipe_pos_id', $recipePosition->id)->missing_amount, FindObjectInArrayByPropertyValue($recipesFulfillment, 'recipe_pos_id', $recipePosition->id)->amount_on_shopping_list) }} @endif</span>
					</td>
					<td class="fit-content">
						<a class="btn btn-sm btn-info recipe-pos-show-note-button @if(empty($recipePosition->note)) disabled @endif" href="#" data-toggle="tooltip" data-placement="top" title="{{ $L('Show notes') }}" data-recipe-pos-note="{{ $recipePosition->note }}">
							<i class="fas fa-eye"></i>
						</a>
					</td>
				</tr>
				@endforeach
				@endif
			</tbody>
		</table>
	</div>
</div>
@stop
