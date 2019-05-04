@extends('layout.default')

@if($mode == 'edit')
	@section('title', $__t('Edit quantity unit'))
@else
	@section('title', $__t('Create quantity unit'))
@endif

@section('viewJsName', 'quantityunitform')

@section('content')
<div class="row">
	<div class="col-lg-6 col-xs-12">
		<h1>@yield('title')</h1>

		<script>Grocy.EditMode = '{{ $mode }}';</script>

		@if($mode == 'edit')
			<script>Grocy.EditObjectId = {{ $quantityunit->id }};</script>
		@endif

		<form id="quantityunit-form" novalidate>

			<div class="form-group">
				<label for="name">{{ $__t('Name') }} <span class="small text-muted">{{ $__t('in singular form') }}</span></label>
				<input type="text" class="form-control" required id="name" name="name" value="@if($mode == 'edit'){{ $quantityunit->name }}@endif">
				<div class="invalid-feedback">{{ $__t('A name is required') }}</div>
			</div>

			<div class="form-group">
				<label for="name_plural">{{ $__t('Name') }} <span class="small text-muted">{{ $__t('in plural form') }}</span></label>
				<input type="text" class="form-control" id="name_plural" name="name_plural" value="@if($mode == 'edit'){{ $quantityunit->name_plural }}@endif">
			</div>

			@if($pluralCount > 2)
			<div class="form-group">
				<label for="plural_forms">
					{{ $__t('Plural forms') }}<br>
					<span class="small text-muted">
						{{ $__t('One plural form per line, the current language requires') }}:<br>
						{{ $__t('Plural count') }}: {{ $pluralCount }}<br>
						{{ $__t('Plural rule') }}: {{ $pluralRule }}
					</span>
				</label>
				<textarea class="form-control" rows="3" id="plural_forms" name="plural_forms">@if($mode == 'edit'){{ $quantityunit->plural_forms }}@endif</textarea>
			</div>
			@endif

			<div class="form-group">
				<label for="description">{{ $__t('Description') }}</label>
				<textarea class="form-control" rows="2" id="description" name="description">@if($mode == 'edit'){{ $quantityunit->description }}@endif</textarea>
			</div>

			@include('components.userfieldsform', array(
				'userfields' => $userfields,
				'entity' => 'quantity_units'
			))

			<button id="save-quantityunit-button" class="btn btn-success">{{ $__t('Save') }}</button>

		</form>
	</div>
</div>
@stop
