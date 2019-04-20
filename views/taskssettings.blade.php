@extends('layout.default')

@section('title', $L('Tasks settings'))

@section('viewJsName', 'taskssettings')

@section('content')
<div class="row">
	<div class="col-lg-6 col-xs-12">
		<h1>@yield('title')</h1>

		@include('components.numberpicker', array(
			'id' => 'tasks_due_soon_days',
			'additionalAttributes' => 'data-setting-key="tasks_due_soon_days"',
			'label' => 'Tasks due soon days',
			'min' => 1,
			'invalidFeedback' => $L('This cannot be lower than #1', '1'),
			'additionalCssClasses' => 'user-setting-control'
		))

		<a href="{{ $U('/tasks') }}" class="btn btn-success">{{ $L('OK') }}</a>
	</div>
</div>
@stop
