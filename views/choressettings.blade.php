@extends('layout.default')

@section('title', $L('Chores settings'))

@section('viewJsName', 'choressettings')

@section('content')
<div class="row">
	<div class="col-lg-6 col-xs-12">
		<h1>@yield('title')</h1>

		<h4 class="mt-2">{{ $L('Chores overview') }}</h4>
		@include('components.numberpicker', array(
			'id' => 'chores_due_soon_days',
			'additionalAttributes' => 'data-setting-key="chores_due_soon_days"',
			'label' => 'Chores due soon days',
			'min' => 1,
			'invalidFeedback' => $L('This cannot be lower than #1', '1'),
			'additionalCssClasses' => 'user-setting-control'
		))

		<a href="{{ $U('/choresoverview') }}" class="btn btn-success">{{ $L('OK') }}</a>
	</div>
</div>
@stop
