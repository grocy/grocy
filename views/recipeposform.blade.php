@extends('layout.default')

@if($mode == 'edit')
	@section('title', $L('Edit recipe ingredient'))
@else
	@section('title', $L('Add recipe ingredient'))
@endif

@section('viewJsName', 'recipeposform')

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
			<script>Grocy.EditObjectId = {{ $recipePos->id }};</script>
		@endif

		<form id="recipe-pos-form" novalidate>

			@php $prefillByName = ''; if($mode=='edit') { $prefillByName = FindObjectInArrayByPropertyValue($products, 'id', $recipePos->product_id)->name; } @endphp
			@include('components.productpicker', array(
				'products' => $products,
				'nextInputSelector' => '#amount',
				'prefillByName' => $prefillByName
			))

			<div class="form-group">
				<label for="amount">{{ $L('Amount') }}&nbsp;&nbsp;<span id="amount_qu_unit" class="small text-muted"></span></label>
				<input type="number" class="form-control" id="amount" name="amount" value="@if($mode == 'edit'){{ $recipePos->amount }}@else{{1}}@endif" min="0" required>
				<div class="invalid-feedback">{{ $L('This cannot be negative') }}</div>
			</div>

			<div class="form-group">
				<label for="note">{{ $L('Note') }}</label>
				<textarea class="form-control" rows="2" id="note" name="note">@if($mode == 'edit'){{ $recipePos->note }}@endif</textarea>
			</div>

			<button id="save-recipe-pos-button" type="submit" class="btn btn-success">{{ $L('Save') }}</button>

		</form>
	</div>

	<div class="col-xs-12 col-md-6 col-xl-4">
		@include('components.productcard')
	</div>
</div>
@stop
