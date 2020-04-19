@extends('layout.default')

@section('title', $__t('Tasks settings'))

@section('viewJsName', 'taskssettings')

@section('content')
<div class="row">
	<div class="col">
		<h2 class="title">@yield('title')</h2>
		<hr>
	</div>
</div>
<div class="row">
	<div class="col-lg-6 col-xs-12">

		@include('components.numberpicker', array(
			'id' => 'tasks_due_soon_days',
			'additionalAttributes' => 'data-setting-key="tasks_due_soon_days"',
			'label' => 'Tasks due soon days',
			'min' => 1,
			'invalidFeedback' => $__t('This cannot be lower than %s', '1'),
			'additionalCssClasses' => 'user-setting-control'
		))

		<a href="{{ $U('/tasks') }}" class="btn btn-success">{{ $__t('OK') }}</a>
	</div>
</div>
@stop
