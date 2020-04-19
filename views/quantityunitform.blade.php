@extends('layout.default')

@if($mode == 'edit')
	@section('title', $__t('Edit quantity unit'))
@else
	@section('title', $__t('Create quantity unit'))
@endif

@section('viewJsName', 'quantityunitform')

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
			<script>Grocy.EditObjectId = {{ $quantityUnit->id }};</script>
		@endif

		<form id="quantityunit-form" novalidate>

			<div class="form-group">
				<label for="name">{{ $__t('Name') }} <span class="small text-muted">{{ $__t('in singular form') }}</span></label>
				<input type="text" class="form-control" required id="name" name="name" value="@if($mode == 'edit'){{ $quantityUnit->name }}@endif">
				<div class="invalid-feedback">{{ $__t('A name is required') }}</div>
			</div>

			<div class="form-group">
				<label for="name_plural">{{ $__t('Name') }} <span class="small text-muted">{{ $__t('in plural form') }}</span></label>
				<input type="text" class="form-control" id="name_plural" name="name_plural" value="@if($mode == 'edit'){{ $quantityUnit->name_plural }}@endif">
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
				<textarea class="form-control" rows="3" id="plural_forms" name="plural_forms">@if($mode == 'edit'){{ $quantityUnit->plural_forms }}@endif</textarea>
			</div>
			@endif

			<div class="form-group">
				<label for="description">{{ $__t('Description') }}</label>
				<textarea class="form-control" rows="2" id="description" name="description">@if($mode == 'edit'){{ $quantityUnit->description }}@endif</textarea>
			</div>

			@include('components.userfieldsform', array(
				'userfields' => $userfields,
				'entity' => 'quantity_units'
			))

			<button id="save-quantityunit-button" class="btn btn-success">{{ $__t('Save') }}</button>

			@if(intval($pluralCount) > 2)
			<button id="test-quantityunit-plural-forms-button" class="btn btn-secondary">{{ $__t('Test plural forms') }}</button>
			@endif

		</form>
	</div>

	<div class="col-lg-6 col-xs-12">
		<h2>
			{{ $__t('Default conversions') }}
			<a id="qu-conversion-add-button" class="btn btn-outline-dark" href="#">
				<i class="fas fa-plus"></i> {{ $__t('Add') }}
			</a>
		</h2>
		<h5 id="qu-conversion-headline-info" class="text-muted font-italic"></h5>
		<table id="qu-conversions-table" class="table table-sm table-striped dt-responsive">
			<thead>
				<tr>
					<th class="border-right"></th>
					<th>{{ $__t('Factor') }}</th>
					<th>{{ $__t('Unit') }}</th>
				</tr>
			</thead>
			<tbody class="d-none">
				@if($mode == "edit")
				@foreach($defaultQuConversions as $defaultQuConversion)
				<tr>
					<td class="fit-content border-right">
						<a class="btn btn-sm btn-info qu-conversion-edit-button" href="#" data-qu-conversion-id="{{ $defaultQuConversion->id }}">
							<i class="fas fa-edit"></i>
						</a>
						<a class="btn btn-sm btn-danger qu-conversion-delete-button" href="#" data-qu-conversion-id="{{ $defaultQuConversion->id }}">
							<i class="fas fa-trash"></i>
						</a>
					</td>
					<td>
						{{ $defaultQuConversion->factor }}
					</td>
					<td>
						{{ FindObjectInArrayByPropertyValue($quantityUnits, 'id', $defaultQuConversion->to_qu_id)->name }}
					</td>
				</tr>
				@endforeach
				@endif
			</tbody>
		</table>
	</div>
</div>
@stop
