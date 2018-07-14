@extends('layout.default')

@section('title', $L('Recipes'))
@section('activeNav', 'recipes')
@section('viewJsName', 'recipes')

@section('content')
<div class="row">
	<div class="col">
		<h1>
			@yield('title')
			<a class="btn btn-outline-dark" href="{{ $U('/recipe/new') }}">
				<i class="fas fa-plus"></i> {{ $L('Add') }}
			</a>
		</h1>
	</div>
</div>

<div class="row mt-3">
	<div class="col-xs-12 col-md-6 col-xl-3">
		<label for="search">{{ $L('Search') }}</label> <i class="fas fa-search"></i>
		<input type="text" class="form-control" id="search">
	</div>
</div>

<div class="row">
	<div class="col-xs-12 col-md-6">
		<table id="recipes-table" class="table table-sm table-striped dt-responsive">
			<thead>
				<tr>
					<th>#</th>
					<th>{{ $L('Name') }}</th>
					<th>{{ $L('Requirements fulfilled') }}</th>
				</tr>
			</thead>
			<tbody>
				@foreach($recipes as $recipe)
				<tr>
					<td class="fit-content">
						<a class="btn btn-sm btn-info" href="{{ $U('/recipe/') }}{{ $recipe->id }}">
							<i class="fas fa-edit"></i>
						</a>
						<a class="btn btn-sm btn-danger recipe-delete-button" href="#" data-recipe-id="{{ $recipe->id }}" data-recipe-name="{{ $recipe->name }}">
							<i class="fas fa-trash"></i>
						</a>
						<a class="btn btn-sm btn-primary recipe-order-missing-button @if(FindObjectInArrayByPropertyValue($recipesSumFulfillment, 'recipe_id', $recipe->id)->need_fulfilled_with_shopping_list == 1){{ disabled }}@endif" href="#" title="{{ $L('Order missing products') }}" data-recipe-id="{{ $recipe->id }}" data-recipe-name="{{ $recipe->name }}">
							<i class="fas fa-shopping-cart"></i>
						</a>
					</td>
					<td>
						{{ $recipe->name }}
					</td>
					<td>
						@if(FindObjectInArrayByPropertyValue($recipesSumFulfillment, 'recipe_id', $recipe->id)->need_fulfilled == 1){{ $L('Yes') }}@else{{ $L('No') }}@endif
						<span class="timeago-contextual">@if(FindObjectInArrayByPropertyValue($recipesSumFulfillment, 'recipe_id', $recipe->id)->need_fulfilled == 1) {{ $L('Enough in stock') }} @else {{ $L('Not enough in stock, #1 ingredients missing', FindObjectInArrayByPropertyValue($recipesSumFulfillment, 'recipe_id', $recipe->id)->missing_products_count) }} @endif</span>
					</td>
				</tr>
				@endforeach
			</tbody>
		</table>
	</div>
</div>
@stop
