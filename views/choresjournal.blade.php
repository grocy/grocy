@extends($rootLayout)

@section('title', $__t('Chores journal'))
@section('activeNav', 'choresjournal')
@section('viewJsName', 'choresjournal')

@php 
$collapsed_none = $embedded ? '' : 'd-md-none';
$collapsed_flex = $embedded ? '' : 'd-md-flex';
@endphp


@section('content')
<div class="row">
	<div class="col">
		<h2 class="title">@yield('title')</h2>
		<div class="float-right">
			<button class="btn btn-outline-dark {{ $collapsed_none }} mt-2 order-1 order-md-3"
				type="button"
				data-toggle="collapse"
				data-target="#table-filter-row">
				<i class="fas fa-filter"></i>
			</button>
		</div>
	</div>
</div>

<hr class="my-2">

<div class="row collapse {{ $collapsed_flex }}"
	id="table-filter-row">
	<div class="col-12 col-md-6 col-xl-3">
		<div class="input-group">
			<div class="input-group-prepend">
				<span class="input-group-text"><i class="fas fa-search"></i></span>
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
				<span class="input-group-text"><i class="fas fa-filter"></i>&nbsp;{{ $__t('Chore') }}</span>
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
	<div class="col">
		<div class="float-right">
			<a id="clear-filter-button"
				class="btn btn-sm btn-outline-info"
				href="#">
				{{ $__t('Clear filter') }}
			</a>
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
							data-toggle="tooltip"
							title="{{ $__t('Table options') }}"
							data-table-selector="#chores-journal-table"
							href="#"><i class="fas fa-eye"></i></a>
					</th>
					<th>{{ $__t('Chore') }}</th>
					<th>{{ $__t('Tracked time') }}</th>
					@if(GROCY_FEATURE_FLAG_CHORES_ASSIGNMENTS)
					<th>{{ $__t('Done by') }}</th>
					@endif
				</tr>
			</thead>
			<tbody class="d-none">
				@foreach($choresLog as $choreLogEntry)
				<tr id="chore-execution-{{ $choreLogEntry->id }}-row"
					class="@if($choreLogEntry->undone == 1) text-muted @endif">
					<td class="fit-content border-right">
						<a class="btn btn-secondary btn-sm undo-chore-execution-button permission-CHORE_UNDO_EXECUTION @if($choreLogEntry->undone == 1) disabled @endif"
							href="#"
							data-execution-id="{{ $choreLogEntry->id }}"
							data-toggle="tooltip"
							data-placement="left"
							title="{{ $__t('Undo chore execution') }}">
							<i class="fas fa-undo"></i>
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
					</td>
					@if(GROCY_FEATURE_FLAG_CHORES_ASSIGNMENTS)
					<td>
						@if ($choreLogEntry->done_by_user_id !== null && !empty($choreLogEntry->done_by_user_id))
						{{ GetUserDisplayName(FindObjectInArrayByPropertyValue($users, 'id', $choreLogEntry->done_by_user_id)) }}
						@else
						{{ $__t('Unknown') }}
						@endif
					</td>
					@endif
				</tr>
				@endforeach
			</tbody>
		</table>
	</div>
</div>
@stop
