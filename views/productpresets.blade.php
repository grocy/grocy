@extends('layout.default')

@section('title', $L('Presets for new products'))

@section('viewJsName', 'productpresets')

@section('content')
<div class="row">
	<div class="col-lg-6 col-xs-12">
		<h1>@yield('title')</h1>

		<div class="form-group">
			<label for="product_presets_location_id">{{ $L('Location') }}</label>
			<select class="form-control user-setting-control" id="product_presets_location_id" data-setting-key="product_presets_location_id">
				<option value="-1"></option>
				@foreach($locations as $location)
					<option value="{{ $location->id }}">{{ $location->name }}</option>
				@endforeach
			</select>
		</div>

		<div class="form-group">
			<label for="product_presets_product_group_id">{{ $L('Product group') }}</label>
			<select class="form-control user-setting-control" id="product_presets_product_group_id" data-setting-key="product_presets_product_group_id">
				<option value="-1"></option>
				@foreach($productgroups as $productgroup)
					<option value="{{ $productgroup->id }}">{{ $productgroup->name }}</option>
				@endforeach
			</select>
		</div>

		<div class="form-group">
			<label for="product_presets_qu_id">{{ $L('Quantity unit') }}</label>
			<select class="form-control user-setting-control" id="product_presets_qu_id" data-setting-key="product_presets_qu_id">
				<option value="-1"></option>
				@foreach($quantityunits as $quantityunit)
					<option value="{{ $quantityunit->id }}">{{ $quantityunit->name }}</option>
				@endforeach
			</select>
		</div>

		<a href="{{ $U('/products') }}" class="btn btn-success">{{ $L('OK') }}</a>
	</div>
</div>
@stop
