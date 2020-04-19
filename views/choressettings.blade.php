@extends('layout.default')

@section('title', $__t('Chores settings'))

@section('viewJsName', 'choressettings')

@section('content')
<div class="row">
	<div class="col">
		<h2 class="title">@yield('title')</h2>
		<hr>
	</div>
</div>
<div class="row">
	<div class="col-lg-6 col-xs-12">
		<h4 class="mt-2">{{ $__t('Chores overview') }}</h4>
		@include('components.numberpicker', array(
			'id' => 'chores_due_soon_days',
			'additionalAttributes' => 'data-setting-key="chores_due_soon_days"',
			'label' => 'Chores due soon days',
			'min' => 1,
			'invalidFeedback' => $__t('This cannot be lower than %s', '1'),
			'additionalCssClasses' => 'user-setting-control'
		))

		<a href="{{ $U('/choresoverview') }}" class="btn btn-success">{{ $__t('OK') }}</a>
	</div>
</div>
@stop
