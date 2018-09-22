@extends('layout.default')

@section('title', $L('Chores analysis'))
@section('activeNav', 'choresanalysis')
@section('viewJsName', 'choresanalysis')

@section('content')
<div class="row">
	<div class="col">
		<h1>@yield('title')</h1>
	</div>
</div>

<div class="row mt-3">
	<div class="col-xs-12 col-md-6 col-xl-3">
		<label for="chore-filter">{{ $L('Filter by chore') }}</label> <i class="fas fa-filter"></i>
		<select class="form-control" id="chore-filter">
			<option value="all">{{ $L('All') }}</option>
			@foreach($chores as $chore)
				<option value="{{ $chore->id }}">{{ $chore->name }}</option>
			@endforeach
		</select>
	</div>
	<div class="col-xs-12 col-md-6 col-xl-3">
		<label for="search">{{ $L('Search') }}</label> <i class="fas fa-search"></i>
		<input type="text" class="form-control" id="search">
	</div>
</div>

<div class="row">
	<div class="col">
		<table id="chores-analysis-table" class="table table-sm table-striped dt-responsive">
			<thead>
				<tr>
					<th>{{ $L('Chore') }}</th>
					<th>{{ $L('Tracked time') }}</th>
					<th>{{ $L('Done by') }}</th>
				</tr>
			</thead>
			<tbody>
				@foreach($choresLog as $choreLogEntry)
				<tr>
					<td>
						{{ FindObjectInArrayByPropertyValue($chores, 'id', $choreLogEntry->chore_id)->name }}
					</td>
					<td>
						{{ $choreLogEntry->tracked_time }}
						<time class="timeago timeago-contextual" datetime="{{ $choreLogEntry->tracked_time  }}"></time>
					</td>
					<td>
						@if ($choreLogEntry->done_by_user_id !== null && !empty($choreLogEntry->done_by_user_id))
						{{ GetUserDisplayName(FindObjectInArrayByPropertyValue($users, 'id', $choreLogEntry->done_by_user_id)) }}
						@else
						{{ $L('Unknown') }}
						@endif
					</td>
				</tr>
				@endforeach
			</tbody>
		</table>
	</div>
</div>
@stop
