@extends('layout.default')

@section('title', $__t('Consume'))
@section('activeNav', 'consume')
@section('viewJsName', 'consume')

@section('content')
<div class="row">
	<div class="col-xs-12 col-md-6 col-xl-4 pb-3">
		<h1>@yield('title')</h1>

		<form id="consume-form" novalidate>

			@include('components.productpicker', array(
				'products' => $products,
				'nextInputSelector' => '#amount',
				'disallowAddProductWorkflows' => true
			))

			@include('components.numberpicker', array(
				'id' => 'amount',
				'label' => 'Amount',
				'hintId' => 'amount_qu_unit',
				'min' => 1,
				'value' => 0,
				'invalidFeedback' => $__t('The amount cannot be lower than %s', '1'),
				'additionalHtmlContextHelp' => '<div id="tare-weight-handling-info" class="text-info font-italic d-none">' . $__t('Tare weight handling enabled - please weigh the whole container, the amount to be posted will be automatically calculcated') . '</div>'
			))

			@if(GROCY_FEATURE_FLAG_STOCK_LOCATION_TRACKING)
                        @php /*@include('components.locationpicker', array(
                                'id' => 'location_id',
                                'locations' => $locations,
                                'isRequired' => true,
                                'label' => 'Location'
                        ))*/ @endphp

                        <div class="form-group">
                                <label for="location_id">{{ $__t('Location') }}</label>
                                <select required class="form-control location-combobox" id="location_id" name="location_id">
                                        <option></option>
                                        @foreach($locations as $location)
                                                <option value="{{ $location->id }}">{{ $location->name }}</option>
                                        @endforeach
                                </select>
                                <div class="invalid-feedback">{{ $__t('A location is required') }}</div>
                        </div>
			@else
			<input type="hidden" name="location_id" id="location_id" value="1">
			@endif

			<div class="form-group">
				<label for="use_specific_stock_entry">
					<input type="checkbox" id="use_specific_stock_entry" name="use_specific_stock_entry"> {{ $__t('Use a specific stock item') }}
					<span class="small text-muted">{{ $__t('The first item in this list would be picked by the default rule which is "First expiring first, then first in first out"') }}</span>
				</label>
				<select disabled class="form-control" id="specific_stock_entry" name="specific_stock_entry">
					<option></option>
				</select>
			</div>

			<div class="checkbox">
				<label for="spoiled">
					<input type="checkbox" id="spoiled" name="spoiled"> {{ $__t('Spoiled') }}
				</label>
			</div>

			@if (GROCY_FEATURE_FLAG_RECIPES)
			@include('components.recipepicker', array(
				'recipes' => $recipes,
				'isRequired' => false,
				'hint' => $__t('This is for statistical purposes only')
			))
			@endif

			<button id="save-consume-button" class="btn btn-success">{{ $__t('OK') }}</button>
			
			@if(GROCY_FEATURE_FLAG_STOCK_PRODUCT_OPENED_TRACKING)
			<button id="save-mark-as-open-button" class="btn btn-secondary">{{ $__t('Mark as opened') }}</button>
			@endif

		</form>
	</div>

	<div class="col-xs-12 col-md-6 col-xl-4 hide-when-embedded">
		@include('components.productcard')
	</div>
</div>
@stop
