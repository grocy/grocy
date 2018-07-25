@extends('layout.default')

@section('title', $L('Habits analysis'))
@section('activeNav', 'habitsanalysis')
@section('viewJsName', 'habitsanalysis')

@section('content')
<div class="row">
	<div class="col">
		<h1>@yield('title')</h1>
	</div>
</div>

<div class="row mt-3">
	<div class="col-xs-12 col-md-6 col-xl-3">
		<label for="habit-filter">{{ $L('Filter by habit') }}</label> <i class="fas fa-filter"></i>
		<select class="form-control" id="habit-filter">
			<option value="all">{{ $L('All') }}</option>
			@foreach($habits as $habit)
				<option value="{{ $habit->id }}">{{ $habit->name }}</option>
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
		<table id="habits-analysis-table" class="table table-sm table-striped dt-responsive">
			<thead>
				<tr>
					<th>{{ $L('Habit') }}</th>
					<th>{{ $L('Tracked time') }}</th>
					<th>{{ $L('Done by') }}</th>
				</tr>
			</thead>
			<tbody>
				@foreach($habitsLog as $habitLogEntry)
				<tr>
					<td>
						{{ FindObjectInArrayByPropertyValue($habits, 'id', $habitLogEntry->habit_id)->name }}
					</td>
					<td>
						{{ $habitLogEntry->tracked_time }}
						<time class="timeago timeago-contextual" datetime="{{ $habitLogEntry->tracked_time  }}"></time>
					</td>
					<td>
						@if ($habitLogEntry->done_by_user_id !== null && !empty($habitLogEntry->done_by_user_id))
						{{ GetUserDisplayName(FindObjectInArrayByPropertyValue($users, 'id', $habitLogEntry->done_by_user_id)) }}
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
