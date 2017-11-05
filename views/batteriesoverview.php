<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">

	<h1 class="page-header">Batteries overview</h1>

	<div class="table-responsive">
		<table id="batteries-overview-table" class="table table-striped">
			<thead>
				<tr>
					<th>Battery</th>
					<th>Last charged</th>
					<th>Next planned charge cycle</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($current as $curentBatteryEntry) : ?>
				<tr class="<?php if (GrocyPhpHelper::FindObjectInArrayByPropertyValue($batteries, 'id', $curentBatteryEntry->battery_id)->charge_interval_days > 0 && GrocyLogicBatteries::GetNextChargeTime($curentBatteryEntry->battery_id) < date('Y-m-d H:i:s')) echo 'error-bg'; ?>">
					<td>
						<?php echo GrocyPhpHelper::FindObjectInArrayByPropertyValue($batteries, 'id', $curentBatteryEntry->battery_id)->name; ?>
					</td>
					<td>
						<?php echo $curentBatteryEntry->last_tracked_time; ?>
						<time class="timeago timeago-contextual" datetime="<?php echo $curentBatteryEntry->last_tracked_time; ?>"></time>
					</td>
					<td>
						<?php if (GrocyPhpHelper::FindObjectInArrayByPropertyValue($batteries, 'id', $curentBatteryEntry->battery_id)->charge_interval_days > 0): ?>
							<?php echo GrocyLogicBatteries::GetNextChargeTime($curentBatteryEntry->battery_id); ?>
							<time class="timeago timeago-contextual" datetime="<?php echo GrocyLogicBatteries::GetNextChargeTime($curentBatteryEntry->battery_id); ?>"></time>
						<?php else: ?>
							...
						<?php endif; ?>
					</td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>

</div>
