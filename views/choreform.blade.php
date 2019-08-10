@extends('layout.default')

@if($mode == 'edit')
	@section('title', $__t('Edit chore'))
@else
	@section('title', $__t('Create chore'))
@endif

@section('viewJsName', 'choreform')

@section('content')
<div class="row">
	<div class="col-lg-6 col-xs-12">
		<h1>@yield('title')</h1>

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
				<label for="period_type">{{ $__t('Period type') }}</label>
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
				'additionalHtmlElements' => '<span id="chore-period-type-info" class="small text-muted"></span>',
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

			@include('components.userfieldsform', array(
				'userfields' => $userfields,
				'entity' => 'chores'
			))

			<button id="save-chore-button" class="btn btn-success">{{ $__t('Save') }}</button>

		</form>
	</div>
</div>
@stop
