@extends($rootLayout)

@section('title', $__t('Chores settings'))

@section('viewJsName', 'choressettings')

@section('content')
<div class="row">
	<div class="col">
		<h2 class="title">@yield('title')</h2>
	</div>
</div>

<hr class="my-2">

<div class="row">
	<div class="col-lg-6 col-12">
		<h4 class="mt-2">{{ $__t('Chores overview') }}</h4>
		@include('components.numberpicker', array(
		'id' => 'chores_due_soon_days',
		'additionalAttributes' => 'data-setting-key="chores_due_soon_days"',
		'label' => 'Due soon days',
		'min' => 1,
		'additionalCssClasses' => 'user-setting-control'
		))

		<a href="{{ $U('/choresoverview') }}"
			class="btn btn-success">{{ $__t('OK') }}</a>
	</div>
</div>
@stop
