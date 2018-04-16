@extends('layout.default')

@section('title', $L('Habits overview'))
@section('activeNav', 'habitsoverview')
@section('viewJsName', 'habitsoverview')

@section('content')
<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2">

	<h1 class="page-header">@yield('title')</h1>

	<div class="table-responsive">
		<table id="habits-overview-table" class="table table-striped">
			<thead>
				<tr>
					<th>{{ $L('Habit') }}</th>
					<th>{{ $L('Next estimated tracking') }}</th>
					<th>{{ $L('Last tracked') }}</th>
				</tr>
			</thead>
			<tbody>
				@foreach($currentHabits as $curentHabitEntry)
				<tr class="@if(FindObjectInArrayByPropertyValue($habits, 'id', $curentHabitEntry->habit_id)->period_type === \Grocy\Services\HabitsService::HABIT_TYPE_DYNAMIC_REGULAR && $nextHabitTimes[$curentHabitEntry->habit_id] < date('Y-m-d H:i:s')) error-bg @endif">
					<td>
						{{ FindObjectInArrayByPropertyValue($habits, 'id', $curentHabitEntry->habit_id)->name }}
					</td>
					<td>
						@if(FindObjectInArrayByPropertyValue($habits, 'id', $curentHabitEntry->habit_id)->period_type === \Grocy\Services\HabitsService::HABIT_TYPE_DYNAMIC_REGULAR)
							{{ $nextHabitTimes[$curentHabitEntry->habit_id] }}
							<time class="timeago timeago-contextual" datetime="{{ $nextHabitTimes[$curentHabitEntry->habit_id] }}"></time>
						@else
							...
						@endif
					</td>
					<td>
						{{ $curentHabitEntry->last_tracked_time }}
						<time class="timeago timeago-contextual" datetime="{{ $curentHabitEntry->last_tracked_time }}"></time>
					</td>
				</tr>
				@endforeach
			</tbody>
		</table>
	</div>

</div>
@stop
