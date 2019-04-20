@extends('layout.default')

@section('title', $L('Batteries settings'))

@section('viewJsName', 'batteriessettings')

@section('content')
<div class="row">
	<div class="col-lg-6 col-xs-12">
		<h1>@yield('title')</h1>

		<h4 class="mt-2">{{ $L('Batteries overview') }}</h4>
		@include('components.numberpicker', array(
			'id' => 'batteries_due_soon_days',
			'additionalAttributes' => 'data-setting-key="batteries_due_soon_days"',
			'label' => 'Batteries due to be charged soon days',
			'min' => 1,
			'invalidFeedback' => $L('This cannot be lower than #1', '1'),
			'additionalCssClasses' => 'user-setting-control'
		))

		<a href="{{ $U('/batteriesoverview') }}" class="btn btn-success">{{ $L('OK') }}</a>
	</div>
</div>
@stop
