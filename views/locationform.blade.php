@extends('layout.default')

@if($mode == 'edit')
	@section('title', $__t('Edit location'))
@else
	@section('title', $__t('Create location'))
@endif

@section('viewJsName', 'locationform')

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
			<script>Grocy.EditObjectId = {{ $location->id }};</script>
		@endif

		<form id="location-form" novalidate>

			<div class="form-group">
				<label for="name">{{ $__t('Name') }}</label>
				<input type="text" class="form-control" required id="name" name="name" value="@if($mode == 'edit'){{ $location->name }}@endif">
				<div class="invalid-feedback">{{ $__t('A name is required') }}</div>
			</div>

			<div class="form-group">
				<label for="description">{{ $__t('Description') }}</label>
				<textarea class="form-control" rows="2" id="description" name="description">@if($mode == 'edit'){{ $location->description }}@endif</textarea>
			</div>

			@if(GROCY_FEATURE_FLAG_STOCK_PRODUCT_FREEZING)
			<div class="form-group">
				<div class="form-check">
					<input type="hidden" name="is_freezer" value="0">
					<input @if($mode == 'edit' && $location->is_freezer == 1) checked @endif class="form-check-input" type="checkbox" id="is_freezer" name="is_freezer" value="1">
					<label class="form-check-label" for="is_freezer">{{ $__t('Is freezer') }}
						<span class="text-muted small">{{ $__t('When moving products from/to a freezer location, the products best before date is automatically adjusted according to the product settings') }}</span>
					</label>
				</div>
			</div>
			@else
			<input type="hidden" name="is_freezer" value="0">
			@endif

			@include('components.userfieldsform', array(
				'userfields' => $userfields,
				'entity' => 'locations'
			))

			<button id="save-location-button" class="btn btn-success">{{ $__t('Save') }}</button>

		</form>
	</div>
</div>
@stop
