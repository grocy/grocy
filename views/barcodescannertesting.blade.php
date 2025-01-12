@php require_frontend_packages(['animatecss']); @endphp

@extends('layout.default')

@section('title', $__t('Barcode scanner testing'))

@section('content')
<div class="row">
	<div class="col">
		<h2 class="title">@yield('title')</h2>
	</div>
</div>

<hr class="my-2">

<div class="row">
	<div class="col-lg-6 col-12">

		<form id="barcodescannertesting-form"
			novalidate>

			<div class="form-group">
				<label for="expected_barcode">{{ $__t('Expected barcode') }}</label>
				<input type="text"
					class="form-control"
					required
					id="expected_barcode"
					name="expected_barcode"
					value="">
			</div>

			<div class="form-group">
				<label for="scanned_barcode">{{ $__t('Scan field') }}</label>
				<div class="input-group">
					<input type="text"
						class="form-control barcodescanner-input"
						id="scanned_barcode"
						name="scanned_barcode"
						value=""
						disabled
						data-target="#scanned_barcode">
				</div>
			</div>

			<div class="form-group">
				<label for="scanned_codes">{{ $__t('Scanned barcodes') }}</label>
				<div class="float-right font-weight-bold">
					<span class="text-success">{{ $__t('Hit') }}: <span id="hit-count"
							class="locale-number locale-number-generic">0</span></span> //
					<span class="text-danger">{{ $__t('Miss') }}: <span id="miss-count"
							class="locale-number locale-number-generic">0</span></span>
				</div>
				<select class="custom-control custom-select"
					id="scanned_codes"
					name="scanned_codes"
					multiple
					size="30"></select>
			</div>

		</form>
	</div>
</div>

@include('components.camerabarcodescanner')
@stop
