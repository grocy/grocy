@php require_frontend_packages(['bootstrap-select']); @endphp

@extends('layout.default')

@if($mode == 'edit')
@section('title', $__t('Edit chore'))
@else
@section('title', $__t('Create chore'))
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
			Grocy.EditObjectId = {{ $chore->id }};
		</script>
		@endif

		<form id="chore-form"
			class="has-sticky-form-footer"
			novalidate>

			<div class="form-group">
				<label for="name">{{ $__t('Name') }}</label>
				<input type="text"
					class="form-control"
					required
					id="name"
					name="name"
					value="@if($mode == 'edit'){{ $chore->name }}@endif">
				<div class="invalid-feedback">{{ $__t('A name is required') }}</div>
			</div>

			<div class="form-group">
				<div class="custom-control custom-checkbox">
					<input @if($mode=='create'
						)
						checked
						@elseif($mode=='edit'
						&&
						$chore->active == 1) checked @endif class="form-check-input custom-control-input" type="checkbox" id="active" name="active" value="1">
					<label class="form-check-label custom-control-label"
						for="active">{{ $__t('Active') }}</label>
				</div>
			</div>

			<div class="form-group">
				<label for="description">{{ $__t('Description') }}</label>
				<textarea class="form-control"
					rows="2"
					id="description"
					name="description">@if($mode == 'edit'){{ $chore->description }}@endif</textarea>
			</div>

			<div class="form-group">
				<label for="period_type">{{ $__t('Period type') }}</label>
				<select required
					class="custom-control custom-select input-group-chore-period-type"
					id="period_type"
					name="period_type">
					@foreach($periodTypes as $periodType)
					<option @if($mode=='edit'
						&&
						$periodType==$chore->period_type) selected="selected" @endif value="{{ $periodType }}">{{ $__t($periodType) }}</option>
					@endforeach
				</select>
				<div class="invalid-feedback">{{ $__t('A period type is required') }}</div>
			</div>

			@php if($mode == 'edit') { $value = $chore->period_days; } else { $value = 0; } @endphp
			@include('components.numberpicker', array(
			'id' => 'period_days',
			'label' => 'Period days',
			'value' => $value,
			'min' => '0',
			'additionalCssClasses' => 'input-group-chore-period-type',
			'additionalGroupCssClasses' => 'period-type-input period-type-monthly'
			))

			<div class="form-group period-type-input period-type-weekly">
				<div class="custom-control custom-checkbox custom-control-inline">
					<input class="form-check-input custom-control-input input-group-chore-period-type"
						type="checkbox"
						id="monday"
						value="monday">
					<label class="form-check-label custom-control-label"
						for="monday">{{ $__t('Monday') }}</label>
				</div>
				<div class="custom-control custom-checkbox custom-control-inline">
					<input class="form-check-input custom-control-input input-group-chore-period-type"
						type="checkbox"
						id="tuesday"
						value="tuesday">
					<label class="form-check-label custom-control-label"
						for="tuesday">{{ $__t('Tuesday') }}</label>
				</div>
				<div class="custom-control custom-checkbox custom-control-inline">
					<input class="form-check-input custom-control-input input-group-chore-period-type"
						type="checkbox"
						id="wednesday"
						value="wednesday">
					<label class="form-check-label custom-control-label"
						for="wednesday">{{ $__t('Wednesday') }}</label>
				</div>
				<div class="custom-control custom-checkbox custom-control-inline">
					<input class="form-check-input custom-control-input input-group-chore-period-type"
						type="checkbox"
						id="thursday"
						value="thursday">
					<label class="form-check-label custom-control-label"
						for="thursday">{{ $__t('Thursday') }}</label>
				</div>
				<div class="custom-control custom-checkbox custom-control-inline">
					<input class="form-check-input custom-control-input input-group-chore-period-type"
						type="checkbox"
						id="friday"
						value="friday">
					<label class="form-check-label custom-control-label"
						for="friday">{{ $__t('Friday') }}</label>
				</div>
				<div class="custom-control custom-checkbox custom-control-inline">
					<input class="form-check-input custom-control-input input-group-chore-period-type"
						type="checkbox"
						id="saturday"
						value="saturday">
					<label class="form-check-label custom-control-label"
						for="saturday">{{ $__t('Saturday') }}</label>
				</div>
				<div class="custom-control custom-checkbox custom-control-inline">
					<input class="form-check-input custom-control-input input-group-chore-period-type"
						type="checkbox"
						id="sunday"
						value="sunday">
					<label class="form-check-label custom-control-label"
						for="sunday">{{ $__t('Sunday') }}</label>
				</div>
			</div>

			<input type="hidden"
				id="period_config"
				name="period_config"
				value="@if($mode == 'edit'){{ $chore->period_config }}@endif">

			@php if($mode == 'edit') { $value = $chore->period_interval; } else { $value = 1; } @endphp
			@include('components.numberpicker', array(
			'id' => 'period_interval',
			'label' => 'Period interval',
			'value' => $value,
			'min' => '1',
			'additionalCssClasses' => 'input-group-chore-period-type',
			'additionalGroupCssClasses' => 'period-type-input period-type-hourly period-type-daily period-type-weekly period-type-monthly period-type-yearly'
			))

			<p id="chore-schedule-info"
				class="form-text text-info mt-n2"></p>

			@php
			$value = date('Y-m-d H:i:s');
			if ($mode == 'edit')
			{
			$value = date('Y-m-d H:i:s', strtotime($chore->start_date));
			}
			@endphp
			@include('components.datetimepicker', array(
			'id' => 'start',
			'label' => 'Start date',
			'initialValue' => $value,
			'format' => 'YYYY-MM-DD HH:mm:ss',
			'initWithNow' => true,
			'limitEndToNow' => false,
			'limitStartToNow' => false,
			'invalidFeedback' => $__t('A start date is required'),
			'hint' => $__t('The start date cannot be changed when the chore was once tracked')
			))

			@if(GROCY_FEATURE_FLAG_CHORES_ASSIGNMENTS)
			<div class="form-group">
				<label for="assignment_type">{{ $__t('Assignment type') }}</label>
				<select required
					class="custom-control custom-select input-group-chore-assignment-type"
					id="assignment_type"
					name="assignment_type">
					@foreach($assignmentTypes as $assignmentType)
					<option @if($mode=='edit'
						&&
						$assignmentType==$chore->assignment_type) selected="selected" @endif value="{{ $assignmentType }}">{{ $__t($assignmentType) }}</option>
					@endforeach
				</select>
				<div class="invalid-feedback">{{ $__t('An assignment type is required') }}</div>
			</div>

			<div class="form-group">
				<label for="assignment_config">{{ $__t('Assign to') }}</label>
				<select required
					multiple
					class="form-control input-group-chore-assignment-type selectpicker"
					id="assignment_config"
					name="assignment_config"
					data-actions-Box="true"
					data-live-search="true">
					@foreach($users as $user)
					<option @if($mode=='edit'
						&&
						in_array($user->id, explode(',', $chore->assignment_config))) selected="selected" @endif value="{{ $user->id }}">{{ $user->display_name }}</option>
					@endforeach
				</select>
				<div class="invalid-feedback">{{ $__t('This assignment type requires that at least one is assigned') }}</div>
			</div>

			<p id="chore-assignment-type-info"
				class="form-text text-info mt-n2"></p>
			@else
			<input type="hidden"
				id="assignment_type"
				name="assignment_type"
				value="{{ \Grocy\Services\ChoresService::CHORE_ASSIGNMENT_TYPE_NO_ASSIGNMENT }}">
			<input type="hidden"
				id="assignment_config"
				name="assignment_config"
				value="">
			@endif

			<div class="form-group">
				<div class="custom-control custom-checkbox">
					<input @if($mode=='edit'
						&&
						$chore->track_date_only == 1) checked @endif class="form-check-input custom-control-input" type="checkbox" id="track_date_only" name="track_date_only" value="1">
					<label class="form-check-label custom-control-label"
						for="track_date_only">{{ $__t('Track date only') }}
						&nbsp;<i class="fa-solid fa-question-circle text-muted"
							data-toggle="tooltip"
							data-trigger="hover click"
							title="{{ $__t('When enabled only the day of an execution is tracked, not the time') }}"></i>
					</label>
				</div>
			</div>

			<div class="form-group">
				<div class="custom-control custom-checkbox">
					<input @if($mode=='edit'
						&&
						$chore->rollover == 1) checked @endif class="form-check-input custom-control-input" type="checkbox" id="rollover" name="rollover" value="1">
					<label class="form-check-label custom-control-label"
						for="rollover">{{ $__t('Due date rollover') }}
						&nbsp;<i class="fa-solid fa-question-circle text-muted"
							data-toggle="tooltip"
							data-trigger="hover click"
							title="{{ $__t('When enabled the chore can never be overdue, the due date will shift forward each day when due') }}"></i>
					</label>
				</div>
			</div>

			@if(GROCY_FEATURE_FLAG_STOCK)
			<div class="form-group mt-4 mb-1">
				<div class="custom-control custom-checkbox">
					<input @if($mode=='edit'
						&&
						$chore->consume_product_on_execution == 1) checked @endif class="form-check-input custom-control-input" type="checkbox" id="consume_product_on_execution" name="consume_product_on_execution" value="1">
					<label class="form-check-label custom-control-label"
						for="consume_product_on_execution">{{ $__t('Consume product on chore execution') }}</label>
				</div>
			</div>

			@php $prefillById = ''; if($mode=='edit' && !empty($chore->product_id)) { $prefillById = $chore->product_id; } @endphp
			@include('components.productpicker', array(
			'products' => $products,
			'nextInputSelector' => '#product_amount',
			'isRequired' => false,
			'disallowAllProductWorkflows' => true,
			'prefillById' => $prefillById
			))

			@php if($mode == 'edit') { $value = $chore->product_amount; } else { $value = ''; } @endphp
			@include('components.numberpicker', array(
			'id' => 'product_amount',
			'label' => 'Amount',
			'contextInfoId' => 'amount_qu_unit',
			'min' => $DEFAULT_MIN_AMOUNT,
			'decimals' => $userSettings['stock_decimal_places_amounts'],
			'isRequired' => false,
			'value' => $value,
			'additionalCssClasses' => 'locale-number-input locale-number-quantity-amount'
			))
			@endif

			@include('components.userfieldsform', array(
			'userfields' => $userfields,
			'entity' => 'chores'
			))

			<div class="sticky-form-footer pt-1">
				<button id="save-chore-button"
					class="btn btn-success">{{ $__t('Save') }}</button>
			</div>

		</form>
	</div>

	<div class="col-lg-6 col-12 @if($mode == 'create') d-none @endif">
		<div class="row">
			<div class="col clearfix">
				<div class="title-related-links pb-4">
					<h4>
						<span class="ls-n1">{{ $__t('Grocycode') }}</span>
						<i class="fa-solid fa-question-circle text-muted"
							data-toggle="tooltip"
							data-trigger="hover click"
							title="{{ $__t('Grocycode is a unique referer to this %s in your Grocy instance - print it onto a label and scan it like any other barcode', $__t('Chore')) }}"></i>
					</h4>
					<p>
						@if($mode == 'edit')
						<img src="{{ $U('/chore/' . $chore->id . '/grocycode?size=60') }}"
							class="float-lg-left"
							loading="lazy">
						@endif
					</p>
					<p>
						<a class="btn btn-outline-primary btn-sm"
							href="{{ $U('/chore/' . $chore->id . '/grocycode?download=true') }}">{{ $__t('Download') }}</a>
						@if(GROCY_FEATURE_FLAG_LABEL_PRINTER)
						<a class="btn btn-outline-primary btn-sm chore-grocycode-label-print"
							data-chore-id="{{ $chore->id }}"
							href="#">
							{{ $__t('Print on label printer') }}
						</a>
						@endif
					</p>
				</div>
			</div>
		</div>
	</div>
</div>
@stop
