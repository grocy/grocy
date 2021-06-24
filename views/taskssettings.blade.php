@extends($rootLayout)

@section('title', $__t('Tasks settings'))

@section('viewJsName', 'taskssettings')

@section('content')
<div class="row">
	<div class="col">
		<h2 class="title">@yield('title')</h2>
	</div>
</div>

<hr class="my-2">

<div class="row">
	<div class="col-lg-6 col-12">

		@include('components.numberpicker', array(
		'id' => 'tasks_due_soon_days',
		'additionalAttributes' => 'data-setting-key="tasks_due_soon_days"',
		'label' => 'Due soon days',
		'min' => 1,
		'additionalCssClasses' => 'user-setting-control'
		))

		<a href="{{ $U('/tasks') }}"
			class="btn btn-success">{{ $__t('OK') }}</a>
	</div>
</div>
@stop
