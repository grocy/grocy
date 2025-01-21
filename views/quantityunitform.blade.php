@php require_frontend_packages(['datatables']); @endphp

@extends('layout.default')

@if($mode == 'edit')
@section('title', $__t('Edit quantity unit'))
@else
@section('title', $__t('Create quantity unit'))
@endif

@section('content')
<div class="row">
	<div class="col">
		<h2 class="title">@yield('title')</h2>
	</div>
</div>

<hr class="my-2">

<div class="row">
	<div class="col-lg-6 col-12">
		<script>
			Grocy.EditMode = '{{ $mode }}';
		</script>

		@if($mode == 'edit')
		<script>
			Grocy.EditObjectId = {{ $quantityUnit->id }};
		</script>
		@endif

		<form id="quantityunit-form"
			novalidate>

			<div class="form-group">
				<label for="name">{{ $__t('Name') }} <span class="small text-muted">{{ $__t('in singular form') }}</span></label>
				<input type="text"
					class="form-control"
					required
					id="name"
					name="name"
					value="@if($mode == 'edit'){{ $quantityUnit->name }}@endif">
				<div class="invalid-feedback">{{ $__t('A name is required') }}</div>
			</div>

			<div class="form-group">
				<label for="name_plural">{{ $__t('Name') }} <span class="small text-muted">{{ $__t('in plural form') }}</span></label>
				<input type="text"
					class="form-control"
					id="name_plural"
					name="name_plural"
					value="@if($mode == 'edit'){{ $quantityUnit->name_plural }}@endif">
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
				<textarea class="form-control"
					rows="3"
					id="plural_forms"
					name="plural_forms">@if($mode == 'edit'){{ $quantityUnit->plural_forms }}@endif</textarea>
			</div>
			@endif

			<div class="form-group">
				<div class="custom-control custom-checkbox">
					<input @if($mode=='create'
						)
						checked
						@elseif($mode=='edit'
						&&
						$quantityUnit->active == 1) checked @endif class="form-check-input custom-control-input" type="checkbox" id="active" name="active" value="1">
					<label class="form-check-label custom-control-label"
						for="active">{{ $__t('Active') }}</label>
				</div>
			</div>

			<div class="form-group">
				<label for="description">{{ $__t('Description') }}</label>
				<textarea class="form-control"
					rows="2"
					id="description"
					name="description">@if($mode == 'edit'){{ $quantityUnit->description }}@endif</textarea>
			</div>

			@include('components.userfieldsform', array(
			'userfields' => $userfields,
			'entity' => 'quantity_units'
			))

			<small class="my-2 form-text text-muted @if($mode == 'edit') d-none @endif">{{ $__t('Save & continue to add conversions') }}</small>

			<button class="save-quantityunit-button btn btn-success mb-2"
				data-location="continue">{{ $__t('Save & continue') }}</button>
			<button class="save-quantityunit-button btn btn-info mb-2"
				data-location="return">{{ $__t('Save & return to quantity units') }}</button>

			@if($pluralCount > 2)
			<button id="test-quantityunit-plural-forms-button"
				class="btn btn-secondary">{{ $__t('Test plural forms') }}</button>
			@endif

		</form>
	</div>

	<div class="col-lg-6 col-12 @if($mode == 'create') d-none @endif">
		<div class="row">
			<div class="col">
				<div class="title-related-links">
					<h4>
						{{ $__t('Default conversions') }}
						<small id="qu-conversion-headline-info"
							class="text-muted font-italic"></small>
					</h4>
					<button class="btn btn-outline-dark d-md-none mt-2 float-right order-1 order-md-3"
						type="button"
						data-toggle="collapse"
						data-target="#related-links">
						<i class="fa-solid fa-ellipsis-v"></i>
					</button>
					<div class="related-links collapse d-md-flex order-2 width-xs-sm-100"
						id="related-links">
						<a class="btn btn-outline-primary btn-sm m-1 mt-md-0 mb-md-0 float-right show-as-dialog-link"
							href="{{ $U('/quantityunitconversion/new?embedded&qu-unit=' . $quantityUnit->id ) }}">
							{{ $__t('Add') }}
						</a>
					</div>
				</div>

				<table id="qu-conversions-table"
					class="table table-sm table-striped nowrap w-100">
					<thead>
						<tr>
							<th class="border-right"><a class="text-muted change-table-columns-visibility-button"
									data-toggle="tooltip"
									title="{{ $__t('Table options') }}"
									data-table-selector="#qu-conversions-table"
									href="#"><i class="fa-solid fa-eye"></i></a>
							</th>
							<th>{{ $__t('Factor') }}</th>
							<th>{{ $__t('Unit') }}</th>
						</tr>
					</thead>
					<tbody class="d-none">
						@if($mode == "edit")
						@foreach($defaultQuConversions as $defaultQuConversion)
						<tr>
							<td class="fit-content border-right">
								<a class="btn btn-sm btn-info show-as-dialog-link"
									href="{{ $U('/quantityunitconversion/' . $defaultQuConversion->id . '?embedded&qu-unit=' . $quantityUnit->id ) }}"
									data-qu-conversion-id="{{ $defaultQuConversion->id }}">
									<i class="fa-solid fa-edit"></i>
								</a>
								<a class="btn btn-sm btn-danger qu-conversion-delete-button"
									href="#"
									data-qu-conversion-id="{{ $defaultQuConversion->id }}">
									<i class="fa-solid fa-trash"></i>
								</a>
							</td>
							<td>
								<span class="locale-number locale-number-quantity-amount">{{ $defaultQuConversion->factor }}</span>
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
	</div>
</div>
@stop
