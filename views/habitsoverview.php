<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">

	<h1 class="page-header">Habits overview</h1>

	<div class="table-responsive">
		<table id="habits-overview-table" class="table table-striped">
			<thead>
				<tr>
					<th>Habit</th>
					<th>Next estimated tracking</th>
					<th>Last tracked</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($currentHabits as $curentHabitEntry) : ?>
				<tr class="<?php if (GrocyPhpHelper::FindObjectInArrayByPropertyValue($habits, 'id', $curentHabitEntry->habit_id)->period_type === GrocyLogicHabits::HABIT_TYPE_DYNAMIC_REGULAR && GrocyLogicHabits::GetNextHabitTime($curentHabitEntry->habit_id) < date('Y-m-d H:i:s')) echo 'error-bg'; ?>">
					<td>
						<?php echo GrocyPhpHelper::FindObjectInArrayByPropertyValue($habits, 'id', $curentHabitEntry->habit_id)->name; ?>
					</td>
					<td>
						<?php if (GrocyPhpHelper::FindObjectInArrayByPropertyValue($habits, 'id', $curentHabitEntry->habit_id)->period_type === GrocyLogicHabits::HABIT_TYPE_DYNAMIC_REGULAR): ?>
							<?php echo GrocyLogicHabits::GetNextHabitTime($curentHabitEntry->habit_id); ?>
							<time class="timeago timeago-contextual" datetime="<?php echo GrocyLogicHabits::GetNextHabitTime($curentHabitEntry->habit_id); ?>"></time>
						<?php else: ?>
							Whenever you want...
						<?php endif; ?>
					</td>
					<td>
						<?php echo $curentHabitEntry->last_tracked_time; ?>
						<time class="timeago timeago-contextual" datetime="<?php echo $curentHabitEntry->last_tracked_time; ?>"></time>
					</td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>

</div>
