@extends('layout.default')

@if($mode == 'edit')
	@section('title', $__t('Edit chore'))
@else
	@section('title', $__t('Create chore'))
@endif

@section('viewJsName', 'choreform')

@section('content')
<div class="row">
	<div class="col">
		<h2 class="title">@yield('title')</h2>
		<hr>
	</div>
</div>
<div class="row">
	<div class="col-lg-6 col-xs-12">
		<script>Grocy.EditMode = '{{ $mode }}';</script>

		@if($mode == 'edit')
			<script>Grocy.EditObjectId = {{ $chore->id }};</script>
		@endif

		<form id="chore-form" novalidate>

			<div class="form-group">
				<label for="name">{{ $__t('Name') }}</label>
				<input type="text" class="form-control" required id="name" name="name" value="@if($mode == 'edit'){{ $chore->name }}@endif">
				<div class="invalid-feedback">{{ $__t('A name is required') }}</div>
			</div>

			<div class="form-group">
				<label for="description">{{ $__t('Description') }}</label>
				<textarea class="form-control" rows="2" id="description" name="description">@if($mode == 'edit'){{ $chore->description }}@endif</textarea>
			</div>

			<div class="form-group">
				<label for="period_type">{{ $__t('Period type') }} <span id="chore-period-type-info" class="small text-muted"></span></label>
				<select required class="form-control input-group-chore-period-type" id="period_type" name="period_type">
					@foreach($periodTypes as $periodType)
						<option @if($mode == 'edit' && $periodType == $chore->period_type) selected="selected" @endif value="{{ $periodType }}">{{ $__t($periodType) }}</option>
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
				'invalidFeedback' => $__t('This cannot be negative'),
				'additionalGroupCssClasses' => 'period-type-input period-type-dynamic-regular period-type-monthly'
			))

			<div class="form-group period-type-input period-type-weekly">
				<div class="form-check form-check-inline">
					<input class="form-check-input input-group-chore-period-type" type="checkbox" id="monday" value="monday">
					<label class="form-check-label" for="monday">{{ $__t('Monday') }}</label>
				</div>
				<div class="form-check form-check-inline">
					<input class="form-check-input input-group-chore-period-type" type="checkbox" id="tuesday" value="tuesday">
					<label class="form-check-label" for="tuesday">{{ $__t('Tuesday') }}</label>
				</div>
				<div class="form-check form-check-inline">
					<input class="form-check-input input-group-chore-period-type" type="checkbox" id="wednesday" value="wednesday">
					<label class="form-check-label" for="wednesday">{{ $__t('Wednesday') }}</label>
				</div>
				<div class="form-check form-check-inline">
					<input class="form-check-input input-group-chore-period-type" type="checkbox" id="thursday" value="thursday">
					<label class="form-check-label" for="thursday">{{ $__t('Thursday') }}</label>
				</div>
				<div class="form-check form-check-inline">
					<input class="form-check-input input-group-chore-period-type" type="checkbox" id="friday" value="friday">
					<label class="form-check-label" for="friday">{{ $__t('Friday') }}</label>
				</div>
				<div class="form-check form-check-inline">
					<input class="form-check-input input-group-chore-period-type" type="checkbox" id="saturday" value="saturday">
					<label class="form-check-label" for="saturday">{{ $__t('Saturday') }}</label>
				</div>
				<div class="form-check form-check-inline">
					<input class="form-check-input input-group-chore-period-type" type="checkbox" id="sunday" value="sunday">
					<label class="form-check-label" for="sunday">{{ $__t('Sunday') }}</label>
				</div>
			</div>

			<input type="hidden" id="period_config" name="period_config" value="@if($mode == 'edit'){{ $chore->period_config }}@endif">

			@php if($mode == 'edit') { $value = $chore->period_interval; } else { $value = 1; } @endphp
			@include('components.numberpicker', array(
				'id' => 'period_interval',
				'label' => 'Period interval',
				'value' => $value,
				'min' => '1',
				'additionalCssClasses' => 'input-group-chore-period-type',
				'invalidFeedback' => $__t('This cannot be lower than %s', '1'),
				'additionalGroupCssClasses' => 'period-type-input period-type-daily period-type-weekly period-type-monthly period-type-yearly',
				'hintId' => 'chore-period-interval-info'
			))

			@if(GROCY_FEATURE_FLAG_CHORES_ASSIGNMENTS)
			<div class="form-group">
				<label for="assignment_type">{{ $__t('Assignment type') }} <span id="chore-assignment-type-info" class="small text-muted"></span></label>
				<select required class="form-control input-group-chore-assignment-type" id="assignment_type" name="assignment_type">
					@foreach($assignmentTypes as $assignmentType)
						<option @if($mode == 'edit' && $assignmentType == $chore->assignment_type) selected="selected" @endif value="{{ $assignmentType }}">{{ $__t($assignmentType) }}</option>
					@endforeach
				</select>
				<div class="invalid-feedback">{{ $__t('An assignment type is required') }}</div>
			</div>

			<div class="form-group">
				<label for="assignment_config">{{ $__t('Assign to') }}</label>
				<select required multiple class="form-control input-group-chore-assignment-type selectpicker" id="assignment_config" name="assignment_config" data-actions-Box="true" data-live-search="true">
					@foreach($users as $user)
						<option @if($mode == 'edit' && in_array($user->id, explode(',', $chore->assignment_config))) selected="selected" @endif value="{{ $user->id }}">{{ $user->display_name }}</option>
					@endforeach
				</select>
				<div class="invalid-feedback">{{ $__t('This assignment type requires that at least one is assigned') }}</div>
			</div>
			@else
			<input type="hidden" id="assignment_type" name="assignment_type" value="{{ \Grocy\Services\ChoresService::CHORE_ASSIGNMENT_TYPE_NO_ASSIGNMENT }}">
			<input type="hidden" id="assignment_config" name="assignment_config" value="">
			@endif

			<div class="form-group">
				<div class="form-check">
					<input type="hidden" name="track_date_only" value="0">
					<input @if($mode == 'edit' && $chore->track_date_only == 1) checked @endif class="form-check-input" type="checkbox" id="track_date_only" name="track_date_only" value="1">
					<label class="form-check-label" for="track_date_only">{{ $__t('Track date only') }}
						<span class="text-muted small">{{ $__t('When enabled only the day of an execution is tracked, not the time') }}</span>
					</label>
				</div>
			</div>

			<div class="form-group">
				<div class="form-check">
					<input type="hidden" name="rollover" value="0">
					<input @if($mode == 'edit' && $chore->rollover == 1) checked @endif class="form-check-input" type="checkbox" id="rollover" name="rollover" value="1">
					<label class="form-check-label" for="rollover">{{ $__t('Due date rollover') }}
						<span class="text-muted small">{{ $__t('When enabled the chore can never be overdue, the due date will shift forward each day when due') }}</span>
					</label>
				</div>
			</div>

			@if(GROCY_FEATURE_FLAG_STOCK)
			<div class="form-group mt-4 mb-1">
				<div class="form-check">
					<input type="hidden" name="consume_product_on_execution" value="0">
					<input @if($mode == 'edit' && $chore->consume_product_on_execution == 1) checked @endif class="form-check-input" type="checkbox" id="consume_product_on_execution" name="consume_product_on_execution" value="1">
					<label class="form-check-label" for="consume_product_on_execution">{{ $__t('Consume product on chore execution') }}</label>
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
				'hintId' => 'amount_qu_unit',
				'min' => 0.0001,
				'step' => 0.0001,
				'invalidFeedback' => $__t('The amount cannot be lower than %s', '1'),
				'isRequired' => false,
				'value' => $value
			))
			@endif

			@include('components.userfieldsform', array(
				'userfields' => $userfields,
				'entity' => 'chores'
			))

			<button id="save-chore-button" class="btn btn-success">{{ $__t('Save') }}</button>

		</form>
	</div>
</div>
@stop
