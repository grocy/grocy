@extends('layout.default')

@if($mode == 'edit')
	@section('title', $L('Edit chore'))
@else
	@section('title', $L('Create chore'))
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
				<label for="name">{{ $L('Name') }}</label>
				<input type="text" class="form-control" required id="name" name="name" value="@if($mode == 'edit'){{ $chore->name }}@endif">
				<div class="invalid-feedback">{{ $L('A name is required') }}</div>
			</div>

			<div class="form-group">
				<label for="description">{{ $L('Description') }}</label>
				<textarea class="form-control" rows="2" id="description" name="description">@if($mode == 'edit'){{ $chore->description }}@endif</textarea>
			</div>

			<div class="form-group">
				<label for="period_type">{{ $L('Period type') }}</label>
				<select required class="form-control input-group-chore-period-type" id="period_type" name="period_type">
					@foreach($periodTypes as $periodType)
						<option @if($mode == 'edit' && $periodType == $chore->period_type) selected="selected" @endif value="{{ $periodType }}">{{ $L($periodType) }}</option>
					@endforeach
				</select>
				<div class="invalid-feedback">{{ $L('A period type is required') }}</div>
			</div>

			@php if($mode == 'edit') { $value = $chore->period_days; } else { $value = 0; } @endphp
			@include('components.numberpicker', array(
				'id' => 'period_days',
				'label' => 'Period days',
				'value' => $value,
				'min' => '0',
				'additionalCssClasses' => 'input-group-chore-period-type',
				'invalidFeedback' => $L('This cannot be negative'),
				'hintId' => 'chore-period-type-info',
				'additionalGroupCssClasses' => 'period-type-input period-type-dynamic-regular period-type-monthly'
			))

			<div class="form-group period-type-input period-type-weekly">
				<div class="form-check form-check-inline">
					<input class="form-check-input input-group-chore-period-type" type="checkbox" id="monday" value="monday">
					<label class="form-check-label" for="monday">{{ $L('Monday') }}</label>
				</div>
				<div class="form-check form-check-inline">
					<input class="form-check-input input-group-chore-period-type" type="checkbox" id="tuesday" value="tuesday">
					<label class="form-check-label" for="tuesday">{{ $L('Tuesday') }}</label>
				</div>
				<div class="form-check form-check-inline">
					<input class="form-check-input input-group-chore-period-type" type="checkbox" id="wednesday" value="wednesday">
					<label class="form-check-label" for="wednesday">{{ $L('Wednesday') }}</label>
				</div>
				<div class="form-check form-check-inline">
					<input class="form-check-input input-group-chore-period-type" type="checkbox" id="thursday" value="thursday">
					<label class="form-check-label" for="thursday">{{ $L('Thursday') }}</label>
				</div>
				<div class="form-check form-check-inline">
					<input class="form-check-input input-group-chore-period-type" type="checkbox" id="friday" value="friday">
					<label class="form-check-label" for="friday">{{ $L('Friday') }}</label>
				</div>
				<div class="form-check form-check-inline">
					<input class="form-check-input input-group-chore-period-type" type="checkbox" id="saturday" value="saturday">
					<label class="form-check-label" for="saturday">{{ $L('Saturday') }}</label>
				</div>
				<div class="form-check form-check-inline">
					<input class="form-check-input input-group-chore-period-type" type="checkbox" id="sunday" value="sunday">
					<label class="form-check-label" for="sunday">{{ $L('Sunday') }}</label>
				</div>
			</div>

			<input type="hidden" id="period_config" name="period_config" value="@if($mode == 'edit'){{ $chore->period_config }}@endif">

			<button id="save-chore-button" class="btn btn-success">{{ $L('Save') }}</button>

		</form>
	</div>
</div>
@stop
