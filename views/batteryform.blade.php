@extends('layout.default')

@if($mode == 'edit')
@section('title', $__t('Edit battery'))
@else
@section('title', $__t('Create battery'))
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
			Grocy.EditObjectId = {{ $battery->id }}
		</script>
		@endif

		<form id="battery-form"
			novalidate>

			<div class="form-group">
				<label for="name">{{ $__t('Name') }}</label>
				<input type="text"
					class="form-control"
					required
					id="name"
					name="name"
					value="@if($mode == 'edit'){{ $battery->name }}@endif">
				<div class="invalid-feedback">{{ $__t('A name is required') }}</div>
			</div>

			<div class="form-group">
				<div class="custom-control custom-checkbox">
					<input @if($mode=='create'
						)
						checked
						@elseif($mode=='edit'
						&&
						$battery->active == 1) checked @endif class="form-check-input custom-control-input" type="checkbox" id="active" name="active" value="1">
					<label class="form-check-label custom-control-label"
						for="active">{{ $__t('Active') }}</label>
				</div>
			</div>

			<div class="form-group">
				<label for="description">{{ $__t('Description') }}</label>
				<input type="text"
					class="form-control"
					id="description"
					name="description"
					value="@if($mode == 'edit'){{ $battery->description }}@endif">
			</div>

			<div class="form-group">
				<label for="name">{{ $__t('Used in') }}</label>
				<input type="text"
					class="form-control"
					id="used_in"
					name="used_in"
					value="@if($mode == 'edit'){{ $battery->used_in }}@endif">
			</div>

			@php if($mode == 'edit') { $value = $battery->charge_interval_days; } else { $value = 0; } @endphp
			@include('components.numberpicker', array(
			'id' => 'charge_interval_days',
			'label' => 'Charge cycle interval (days)',
			'value' => $value,
			'min' => '0',
			'hint' => $__t('0 means suggestions for the next charge cycle are disabled')
			))

			@include('components.userfieldsform', array(
			'userfields' => $userfields,
			'entity' => 'batteries'
			))

			<button id="save-battery-button"
				class="btn btn-success">{{ $__t('Save') }}</button>

		</form>
	</div>
</div>

@if($mode == 'edit')
<div class="row mt-2 border-top">
	<div class="col clearfix mt-2">
		<div class="title-related-links">
			<h4>
				<span class="ls-n1">{{ $__t('Grocycode') }}</span>
				<i class="fa-solid fa-question-circle text-muted"
					data-toggle="tooltip"
					data-trigger="hover click"
					title="{{ $__t('Grocycode is a unique referer to this %s in your Grocy instance - print it onto a label and scan it like any other barcode', $__t('Battery')) }}"></i>
			</h4>
			<p>
				@if($mode == 'edit')
				<img src="{{ $U('/battery/' . $battery->id . '/grocycode?size=60') }}"
					class="float-lg-left"
					loading="lazy">
				@endif
			</p>
			<p>
				<a class="btn btn-outline-primary btn-sm"
					href="{{ $U('/battery/' . $battery->id . '/grocycode?download=true') }}">{{ $__t('Download') }}</a>
				@if(GROCY_FEATURE_FLAG_LABEL_PRINTER)
				<a class="btn btn-outline-primary btn-sm battery-grocycode-label-print"
					data-battery-id="{{ $battery->id }}"
					href="#">
					{{ $__t('Print on label printer') }}
				</a>
				@endif
			</p>
		</div>
	</div>
</div>
@endif
@stop
