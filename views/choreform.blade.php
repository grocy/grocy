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
				'additionalHtmlElements' => '<p id="chore-period-type-info" class="form-text text-muted small d-none"></p>'
			))

			<button id="save-chore-button" type="submit" class="btn btn-success">{{ $L('Save') }}</button>

		</form>
	</div>
</div>
@stop
