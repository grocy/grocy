@extends('layout.default')

@section('title', $__t('Chores journal'))
@section('activeNav', 'choresjournal')
@section('viewJsName', 'choresjournal')

@section('content')
<div class="row">
	<div class="col">
		<h2 class="title">@yield('title')</h2>
	</div>
</div>

<hr>
<div class="row my-3">
	<div class="col-xs-12 col-md-6 col-xl-3">
		<div class="input-group mb-3">
			<div class="input-group-prepend">
					<span class="input-group-text"><i class="fas fa-search"></i></span>
			</div>
			<input type="text"  id="search" class="form-control" placeholder="{{ $__t('Search') }}">
		</div>
	</div>
	<div class="col-xs-12 col-md-6 col-xl-3">
		<div class="input-group mb-3">
			<div class="input-group-prepend">
					<span class="input-group-text"><i class="fas fa-filter"></i></span>
			</div>
			<select class="form-control" id="chore-filter">
				<option value="all">{{ $__t('All') }}</option>
				@foreach($chores as $chore)
					<option value="{{ $chore->id }}">{{ $chore->name }}</option>
				@endforeach
			</select>
		</div>
	</div>
</div>

<div class="row">
	<div class="col">
		<table id="chores-journal-table" class="table table-sm table-striped dt-responsive">
			<thead>
				<tr>
					<th class="border-right"></th>
					<th>{{ $__t('Chore') }}</th>
					<th>{{ $__t('Tracked time') }}</th>
					@if(GROCY_FEATURE_FLAG_CHORES_ASSIGNMENTS)
					<th>{{ $__t('Done by') }}</th>
					@endif
				</tr>
			</thead>
			<tbody class="d-none">
				@foreach($choresLog as $choreLogEntry)
				<tr id="chore-execution-{{ $choreLogEntry->id }}-row" class="@if($choreLogEntry->undone == 1) text-muted @endif">
					<td class="fit-content border-right">
						<a class="btn btn-secondary btn-sm undo-chore-execution-button @if($choreLogEntry->undone == 1) disabled @endif" href="#" data-execution-id="{{ $choreLogEntry->id }}" data-toggle="tooltip" data-placement="left" title="{{ $__t('Undo chore execution') }}">
							<i class="fas fa-undo"></i>
						</a>
					</td>
					<td>
						<span class="name-anchor @if($choreLogEntry->undone == 1) text-strike-through @endif">{{ FindObjectInArrayByPropertyValue($chores, 'id', $choreLogEntry->chore_id)->name }}</span>
						@if($choreLogEntry->undone == 1)
						<br>
						{{ $__t('Undone on') . ' ' . $choreLogEntry->undone_timestamp }}
						<time class="timeago timeago-contextual" datetime="{{ $choreLogEntry->undone_timestamp }}"></time>
						@endif
					</td>
					<td>
						<span>{{ $choreLogEntry->tracked_time }}</span>
						<time class="timeago timeago-contextual @if(FindObjectInArrayByPropertyValue($chores, 'id', $choreLogEntry->chore_id)->track_date_only == 1) timeago-date-only @endif" datetime="{{ $choreLogEntry->tracked_time }}"></time>
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
