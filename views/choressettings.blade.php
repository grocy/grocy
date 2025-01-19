@extends('layout.default')

@section('title', $__t('Chores settings'))

@section('content')
<div class="row">
	<div class="col">
		<h2 class="title">@yield('title')</h2>
	</div>
</div>

<hr class="my-2">

<div class="row">
	<div class="col-lg-4 col-md-8 col-12">
		<h4 ">{{ $__t('Chores overview') }}</h4>

		@include('components.numberpicker', array(
		'id' => 'chores_due_soon_days',
		'additionalAttributes' => 'data-setting-key=" chores_due_soon_days"', 'label'=> 'Due soon days',
			'min' => 0,
			'additionalCssClasses' => 'user-setting-control',
			'hint' => $__t('Set to 0 to hide due soon filters/highlighting')
			))

			<div class="form-group">
				<div class="custom-control custom-checkbox">
					<input type="checkbox"
						class="form-check-input custom-control-input user-setting-control"
						id="chores_overview_swap_tracking_buttons"
						data-setting-key="chores_overview_swap_tracking_buttons">
					<label class="form-check-label custom-control-label"
						for="chores_overview_swap_tracking_buttons">
						{{ $__t('Swap track next schedule / track now buttons') }}
					</label>
				</div>
			</div>

			<a href="{{ $U('/choresoverview') }}"
				class="btn btn-success">{{ $__t('OK') }}</a>
	</div>
</div>
@stop
