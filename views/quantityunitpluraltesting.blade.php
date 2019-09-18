@extends('layout.default')

@section('title', $__t('Quantity unit plural form testing'))

@section('viewJsName', 'quantityunitpluraltesting')

@push('pageScripts')
	<script src="{{ $U('/node_modules/jquery-ui-dist/jquery-ui.min.js?v=', true) }}{{ $version }}"></script>
@endpush

@section('content')
<div class="row">
	<div class="col-lg-6 col-xs-12">
		<h1>@yield('title')</h1>

		<form id="quantityunitpluraltesting-form" novalidate>

			<div class="form-group">
				<label for="qu_id">{{ $__t('Quantity unit') }}</label>
				<select class="form-control" id="qu_id" name="qu_id">
					<option></option>
					@foreach($quantityUnits as $quantityUnit)
						<option value="{{ $quantityUnit->id }}" data-singular-form="{{ $quantityUnit->name }}" data-plural-form="{{ $quantityUnit->name_plural }}">{{ $quantityUnit->name }}</option>
					@endforeach
				</select>
			</div>

			@include('components.numberpicker', array(
				'id' => 'amount',
				'label' => 'Amount',
				'min' => 0.0001,
				'step' => 1,
				'isRequired' => false,
				'value' => 1
			))

		</form>

		<h2><strong>{{ $__t('Result') }}:</strong> <span id="result"></span></h2>
	</div>
</div>
@stop
