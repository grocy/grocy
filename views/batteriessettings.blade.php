@extends('layout.default')

@section('title', $__t('Batteries settings'))

@section('viewJsName', 'batteriessettings')

@section('content')
<div class="row">
	<div class="col">
		<h2 class="title">@yield('title')</h2>
	</div>
</div>

<hr class="my-2">

<div class="row">
	<div class="col-lg-6 col-12">
		<h4 class="mt-2">{{ $__t('Batteries overview') }}</h4>
		@include('components.numberpicker', array(
		'id' => 'batteries_due_soon_days',
		'additionalAttributes' => 'data-setting-key="batteries_due_soon_days"',
		'label' => 'Due soon days',
		'min' => 0,
		'additionalCssClasses' => 'user-setting-control',
		'hint' => $__t('Set to 0 to hide due soon filters/highlighting')
		))

		<a href="{{ $U('/batteriesoverview') }}"
			class="btn btn-success">{{ $__t('OK') }}</a>
	</div>
</div>
@stop
