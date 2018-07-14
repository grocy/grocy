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
	<div class="col-xs-12 col-md-6 pb-3">
		<form id="recipe-form" novalidate>

			<div class="form-group">
				<label for="name">{{ $L('Name') }}</label>
				<input type="text" class="form-control" required id="name" name="name" value="@if($mode == 'edit'){{ $recipe->name }}@endif">
				<div class="invalid-feedback">{{ $L('A name is required') }}</div>
			</div>

			<div class="form-group">
				<label for="description">{{ $L('Description') }}</label>
				<textarea id="description" class="form-control" name="description" rows="25">@if($mode == 'edit'){{ $recipe->description }}@endif</textarea>
			</div>

			<button id="save-recipe-button" type="submit" class="btn btn-success">{{ $L('Save') }}</button>

		</form>
	</div>

	<div class="col-xs-12 col-md-6 pb-3">
		<h2>
			{{ $L('Ingredients list') }}
			<a class="btn btn-outline-dark" href="{{ $U('/recipe/' . $recipe->id . '/pos/new') }}">
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
				<tr class="@if(FindObjectInArrayByPropertyValue($recipesFulfillment, 'recipe_pos_id', $recipePosition->id)->need_fullfiled == 1) table-success @endif">
					<td class="fit-content">
						<a class="btn btn-sm btn-info" href="{{ $U('/recipe/' . $recipe->id . '/pos/' . $recipePosition->id) }}">
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
						{{ $recipePosition->amount }} {{ FindObjectInArrayByPropertyValue($quantityunits, 'id', FindObjectInArrayByPropertyValue($products, 'id', $recipePosition->product_id)->qu_id_stock)->name }}
						<span class="timeago-contextual">@if(FindObjectInArrayByPropertyValue($recipesFulfillment, 'recipe_pos_id', $recipePosition->id)->need_fullfiled == 1) {{ $L('Enough in stock') }} @else {{ $L('Not enough in stock') }} @endif</span>
					</td>
					<td>
						@if(strlen($recipePosition->note) > 50)
							{{ substr($recipePosition->note, 0, 50) }}...
						@else
							{{ $recipePosition->note }}
						@endif
					</td>
				</tr>
				@endforeach
				@endif
			</tbody>
		</table>
	</div>
</div>
@stop
