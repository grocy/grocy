@extends('layout.default')

@if($mode == 'edit')
	@section('title', $L('Edit included recipe'))
@else
	@section('title', $L('Add included recipe'))
@endif

@section('viewJsName', 'recipeincludeform')

@section('content')
<div class="row">
	<div class="col-xs-12 col-md-6 col-xl-5 pb-3">
		<h1>@yield('title')</h1>
		<h3 class="text-muted">{{ $L('Recipe') }} <strong>{{ $recipe->name }}</strong></h3>

		<script>
			Grocy.EditMode = '{{ $mode }}';
			Grocy.EditObjectParentId = {{ $recipe->id }};
		</script>

		@if($mode == 'edit')
			<script>Grocy.EditObjectId = {{ $recipeInclude->id }};</script>
		@endif

		<form id="recipe-include-form" novalidate>

			<div class="form-group">
				<label for="includes_recipe_id">{{ $L('Recipe') }}</label>
				<select required class="form-control" id="includes_recipe_id" name="includes_recipe_id">
					<option></option>
					@foreach($recipes as $recipe)
						<option @if($mode == 'edit' && $recipe->id == $recipeInclude->includes_recipe_id) selected="selected" @endif value="{{ $recipe->id }}">{{ $recipe->name }}</option>
					@endforeach
				</select>
				<div class="invalid-feedback">{{ $L('A recipe is required') }}</div>
			</div>

			<button id="save-recipe-include-button" class="btn btn-success">{{ $L('Save') }}</button>

		</form>
	</div>
</div>
@stop
