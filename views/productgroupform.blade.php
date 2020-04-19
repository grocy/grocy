@extends('layout.default')

@if($mode == 'edit')
	@section('title', $__t('Edit product group'))
@else
	@section('title', $__t('Create product group'))
@endif

@section('viewJsName', 'productgroupform')

@section('content')
<div class="row">
	<div class="col">
		<h2 class="title">@yield('title')</h2>
		<hr>
	</div>
</div>
<div class="row">
	<div class="col-lg-6 col-xs-12">
		<script>Grocy.EditMode = '{{ $mode }}';</script>

		@if($mode == 'edit')
			<script>Grocy.EditObjectId = {{ $group->id }};</script>
		@endif

		<form id="product-group-form" novalidate>

			<div class="form-group">
				<label for="name">{{ $__t('Name') }}</label>
				<input type="text" class="form-control" required id="name" name="name" value="@if($mode == 'edit'){{ $group->name }}@endif">
				<div class="invalid-feedback">{{ $__t('A name is required') }}</div>
			</div>

			<div class="form-group">
				<label for="description">{{ $__t('Description') }}</label>
				<textarea class="form-control" rows="2" id="description" name="description">@if($mode == 'edit'){{ $group->description }}@endif</textarea>
			</div>

			@include('components.userfieldsform', array(
				'userfields' => $userfields,
				'entity' => 'product_groups'
			))

			<button id="save-product-group-button" class="btn btn-success">{{ $__t('Save') }}</button>

		</form>
	</div>
</div>
@stop
