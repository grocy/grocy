@php require_frontend_packages(['datatables']); @endphp

@extends('layout.default')

@section('title', $__t('Chores journal'))

@section('content')
<div class="row">
	<div class="col">
		<h2 class="title">@yield('title')</h2>
		<div class="float-right @if($embedded) pr-5 @endif">
			<button class="btn btn-outline-dark d-md-none mt-2 order-1 order-md-3"
				type="button"
				data-toggle="collapse"
				data-target="#table-filter-row">
				<i class="fa-solid fa-filter"></i>
			</button>
		</div>
	</div>
</div>

<hr class="my-2">

<div class="row collapse d-md-flex"
	id="table-filter-row">
	<div class="col-12 col-md-6 col-xl-3">
		<div class="input-group">
			<div class="input-group-prepend">
				<span class="input-group-text"><i class="fa-solid fa-search"></i></span>
			</div>
			<input type="text"
				id="search"
				class="form-control"
				placeholder="{{ $__t('Search') }}">
		</div>
	</div>
	<div class="col-12 col-md-6 col-xl-3">
		<div class="input-group">
			<div class="input-group-prepend">
				<span class="input-group-text"><i class="fa-solid fa-filter"></i>&nbsp;{{ $__t('Chore') }}</span>
			</div>
			<select class="custom-control custom-select"
				id="chore-filter">
				<option value="all">{{ $__t('All') }}</option>
				@foreach($chores as $chore)
				<option value="{{ $chore->id }}">{{ $chore->name }}</option>
				@endforeach
			</select>
		</div>
	</div>
	<div class="col-12 col-md-6 col-xl-3">
		<div class="input-group">
			<div class="input-group-prepend">
				<span class="input-group-text"><i class="fa-solid fa-clock"></i>&nbsp;{{ $__t('Date range') }}</span>
			</div>
			<select class="custom-control custom-select"
				id="daterange-filter">
				<option value="1">{{ $__n(1, '%s month', '%s months') }}</option>
				<option value="6">{{ $__n(6, '%s month', '%s months') }}</option>
				<option value="12"
					selected>{{ $__n(1, '%s year', '%s years') }}</option>
				<option value="24">{{ $__n(2, '%s month', '%s years') }}</option>
				<option value="9999">{{ $__t('All') }}</option>
			</select>
		</div>
	</div>
	<div class="col">
		<div class="float-right">
			<button id="clear-filter-button"
				class="btn btn-sm btn-outline-info"
				data-toggle="tooltip"
				title="{{ $__t('Clear filter') }}">
				<i class="fa-solid fa-filter-circle-xmark"></i>
			</button>
		</div>
	</div>
</div>

<div class="row mt-2">
	<div class="col">
		<table id="chores-journal-table"
			class="table table-sm table-striped nowrap w-100">
			<thead>
				<tr>
					<th class="border-right"><a class="text-muted change-table-columns-visibility-button"
							data-toggle="tooltip"
							title="{{ $__t('Table options') }}"
							data-table-selector="#chores-journal-table"
							href="#"><i class="fa-solid fa-eye"></i></a>
					</th>
					<th class="allow-grouping">{{ $__t('Chore') }}</th>
					<th>{{ $__t('Tracked time') }}</th>
					<th>{{ $__t('Scheduled tracking time') }}</th>
					<th>{{ $__t('Time of tracking') }}</th>
					<th class="allow-grouping @if(!GROCY_FEATURE_FLAG_CHORES_ASSIGNMENTS) d-none @endif">{{ $__t('Done by') }}</th>

					@include('components.userfields_thead', array(
					'userfields' => $userfields
					))
				</tr>
			</thead>
			<tbody class="d-none">
				@foreach($choresLog as $choreLogEntry)
				<tr id="chore-execution-{{ $choreLogEntry->id }}-row"
					class="@if($choreLogEntry->undone == 1) text-muted @endif @if($choreLogEntry->skipped == 1) font-italic @endif @if (!empty($choreLogEntry->scheduled_execution_time) && $choreLogEntry->skipped == 0 && $choreLogEntry->tracked_time > $choreLogEntry->scheduled_execution_time) table-danger @endif">
					<td class="fit-content border-right">
						<a class="btn btn-secondary btn-xs undo-chore-execution-button permission-CHORE_UNDO_EXECUTION @if($choreLogEntry->undone == 1) disabled @endif"
							href="#"
							data-execution-id="{{ $choreLogEntry->id }}"
							data-toggle="tooltip"
							data-placement="left"
							title="{{ $__t('Undo chore execution') }}">
							<i class="fa-solid fa-undo"></i>
						</a>
					</td>
					<td>
						<span class="name-anchor @if($choreLogEntry->undone == 1) text-strike-through @endif">{{ FindObjectInArrayByPropertyValue($chores, 'id', $choreLogEntry->chore_id)->name }}</span>
						@if($choreLogEntry->undone == 1)
						<br>
						{{ $__t('Undone on') . ' ' . $choreLogEntry->undone_timestamp }}
						<time class="timeago timeago-contextual"
							datetime="{{ $choreLogEntry->undone_timestamp }}"></time>
						@endif
					</td>
					<td>
						<span>{{ $choreLogEntry->tracked_time }}</span>
						<time class="timeago timeago-contextual @if(FindObjectInArrayByPropertyValue($chores, 'id', $choreLogEntry->chore_id)->track_date_only == 1) timeago-date-only @endif"
							datetime="{{ $choreLogEntry->tracked_time }}"></time>
						@if($choreLogEntry->skipped == 1)
						<span class="text-muted">{{ $__t('Skipped') }}</span>
						@endif
					</td>
					<td>
						@if (!empty($choreLogEntry->scheduled_execution_time))
						<span>{{ $choreLogEntry->scheduled_execution_time }}</span>
						<time class="timeago timeago-contextual @if(FindObjectInArrayByPropertyValue($chores, 'id', $choreLogEntry->chore_id)->track_date_only == 1) timeago-date-only @endif"
							datetime="{{ $choreLogEntry->scheduled_execution_time }}"></time>
						@endif
					</td>
					<td>
						<span>{{ $choreLogEntry->row_created_timestamp }}</span>
						<time class="timeago timeago-contextual"
							datetime="{{ $choreLogEntry->row_created_timestamp }}"></time>
					</td>
					<td class="@if(!GROCY_FEATURE_FLAG_CHORES_ASSIGNMENTS) d-none @endif">
						@if ($choreLogEntry->done_by_user_id !== null && !empty($choreLogEntry->done_by_user_id))
						{{ GetUserDisplayName(FindObjectInArrayByPropertyValue($users, 'id', $choreLogEntry->done_by_user_id)) }}
						@else
						{{ $__t('Unknown') }}
						@endif
					</td>

					@include('components.userfields_tbody', array(
					'userfields' => $userfields,
					'userfieldValues' => FindAllObjectsInArrayByPropertyValue($userfieldValues, 'object_id', $choreLogEntry->id)
					))
				</tr>
				@endforeach
			</tbody>
		</table>
	</div>
</div>
@stop
