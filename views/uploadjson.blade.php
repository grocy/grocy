@extends('layout.default')

@section('title', $__t('Upload Purchase Data As JSON'))
@section('activeNav', 'uploadjson')
@section('viewJsName', 'uploadjson')

@section('content')
<div class="row">
	<div class="col-md-12">
		<h1>
			@yield('title')
		</h1>
	</div>
</div>
<div class="row">
	<div class="col-lg-6 col-xs-12">
		<form id="json-form" novalidate>

			@if(GROCY_FEATURE_FLAG_STOCK_LOCATION_TRACKING)
			<div class="form-group">
				<label for="location_id">{{ $__t('Default location ') . $location->name }}</label>
				<p class="small text-muted">{{ $__t('Change this value in user settings') }}</p>
			</div>
			@endif
			
			<div class="form-group">
				<label for="qu_id_purchase">{{ $__t('Default Quantity unit ') . $quantityunit->name }}</label>
				<p class="small text-muted">{{ $__t('Change this value in user settings') }}</p>
			</div>
			
			<div class="form-group">
				<label for="shopping_location_id">{{ $__t('Default store') }}</label>
				<select class="form-control input-group-qu" id="shopping_location_id" name="shopping_location_id">
					<option></option>
					@foreach($shoppinglocations as $location)
						<option value="{{ $location->id }}">{{ $location->name }}</option>
					@endforeach
				</select>
			</div>

			<div class="form-group">
				<div class="form-check">
					<input class="form-check-input" type="checkbox" id="dont_add_to_stock" name="dont_add_to_stock" value="1">
					<label class="form-check-label" for="dont_add_to_stock">{{ $__t("Don't add to stock") }}</label>
				</div>
			</div>

            <div class="form-group">
                <label for="json-data">{{ $__t('JSON Data') }}</label>
                <textarea class="form-control" rows="75" id="json-data" name="json-data" placeholder="{'data': [{'items': [{}]}] }"></textarea>
            </div>

			<button id="upload-json-button" class="btn btn-success d-block">{{ $__t('OK') }}</button>

		</form>
	</div>
	<div class="col-lg-6 col-xs-12">
		<p>
			{{ $__t("Kroger grocery stores allow manual access to past purchases in json form 
			through a browser's developer tools.") }}
		</p>
		<p>
			{{ $__t("To get this json, open developer tools
			and navigate to https://www.qfc.com/mypurchases (or another Kroger grocer,
			such as https://www.fredmeyer.com/mypurchases) and look for a call to 
			/mypurchases/api/v1/receipt/details.  The response will contain data for the
			last five receipts.") }}
		</p>
	</div>
</div>
@stop
