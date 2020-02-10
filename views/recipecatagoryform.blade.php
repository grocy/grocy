@extends('layout.default')

@if($mode == 'edit')
	@section('title', $__t('Edit recipe catagory'))
@else
	@section('title', $__t('Create recipe catagory'))
@endif

@section('viewJsName', 'recipecatagoryform')

@section('content')
<div class="row">
	<div class="col-lg-6 col-xs-12">
		<h1>@yield('title')</h1>

		<script>Grocy.EditMode = '{{ $mode }}';</script>

		@if($mode == 'edit')
			<script>Grocy.EditObjectId = {{ $recipecatagory->id }};</script>
		@endif

		<form id="recipe-catagory-form" novalidate>

			<div class="form-group">
				<label for="name">{{ $__t('Name') }}</label>
				<input type="text" class="form-control" required id="name" name="name" value="@if($mode == 'edit'){{ $recipecatagory->name }}@endif">
				<div class="invalid-feedback">{{ $__t('A name is required') }}</div>
			</div>

			<div class="form-group">
				<label for="description">{{ $__t('Description') }}</label>
				<textarea class="form-control" rows="2" id="description" name="description">@if($mode == 'edit'){{ $recipecatagory->description }}@endif</textarea>
			</div>


			<button id="save-recipe-catagory-button" class="btn btn-success">{{ $__t('Save') }}</button>

		</form>
	</div>
</div>
@stop
