<?php $__env->startSection('title', $__t('Batteries overview')); ?>
<?php $__env->startSection('activeNav', 'batteriesoverview'); ?>
<?php $__env->startSection('viewJsName', 'batteriesoverview'); ?>

<?php $__env->startPush('pageScripts'); ?>
	<script src="<?php echo e($U('/node_modules/jquery-ui-dist/jquery-ui.min.js?v=', true)); ?><?php echo e($version); ?>"></script>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="row">
	<div class="col">
		<h1><?php echo $__env->yieldContent('title'); ?>
			<a class="btn btn-outline-dark responsive-button" href="<?php echo e($U('/batteriesjournal')); ?>">
				<i class="fas fa-file-alt"></i> <?php echo e($__t('Journal')); ?>

			</a>
		</h1>
		<p id="info-due-batteries" data-status-filter="duesoon" data-next-x-days="<?php echo e($nextXDays); ?>" class="btn btn-lg btn-warning status-filter-button responsive-button mr-2"></p>
		<p id="info-overdue-batteries" data-status-filter="overdue" class="btn btn-lg btn-danger status-filter-button responsive-button"></p>
	</div>
</div>

<div class="row mt-3">
	<div class="col-xs-12 col-md-6 col-xl-3">
		<label for="search"><?php echo e($__t('Search')); ?></label> <i class="fas fa-search"></i>
		<input type="text" class="form-control" id="search">
	</div>
	<div class="col-xs-12 col-md-6 col-xl-3">
		<label for="status-filter"><?php echo e($__t('Filter by status')); ?></label> <i class="fas fa-filter"></i>
		<select class="form-control" id="status-filter">
			<option class="bg-white" value="all"><?php echo e($__t('All')); ?></option>
			<option class="bg-warning" value="duesoon"><?php echo e($__t('Due soon')); ?></option>
			<option class="bg-danger" value="overdue"><?php echo e($__t('Overdue')); ?></option>
		</select>
	</div>
</div>

<div class="row">
	<div class="col">
		<table id="batteries-overview-table" class="table table-sm table-striped dt-responsive">
			<thead>
				<tr>
					<th class="border-right"></th>
					<th><?php echo e($__t('Battery')); ?></th>
					<th><?php echo e($__t('Last charged')); ?></th>
					<th><?php echo e($__t('Next planned charge cycle')); ?></th>
					<th class="d-none">Hidden status</th>

					<?php echo $__env->make('components.userfields_thead', array(
						'userfields' => $userfields
					), \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
					
				</tr>
			</thead>
			<tbody class="d-none">
				<?php $__currentLoopData = $current; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $curentBatteryEntry): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
				<tr id="battery-<?php echo e($curentBatteryEntry->battery_id); ?>-row" class="<?php if(FindObjectInArrayByPropertyValue($batteries, 'id', $curentBatteryEntry->battery_id)->charge_interval_days > 0 && $curentBatteryEntry->next_estimated_charge_time < date('Y-m-d H:i:s')): ?> table-danger <?php elseif(FindObjectInArrayByPropertyValue($batteries, 'id', $curentBatteryEntry->battery_id)->charge_interval_days > 0 && $curentBatteryEntry->next_estimated_charge_time < date('Y-m-d H:i:s', strtotime("+$nextXDays days"))): ?> table-warning <?php endif; ?>">
					<td class="fit-content border-right">
						<a class="btn btn-success btn-sm track-charge-cycle-button" href="#" data-toggle="tooltip" data-placement="left" title="<?php echo e($__t('Track charge cycle of battery %s', FindObjectInArrayByPropertyValue($batteries, 'id', $curentBatteryEntry->battery_id)->name)); ?>"
							data-battery-id="<?php echo e($curentBatteryEntry->battery_id); ?>"
							data-battery-name="<?php echo e(FindObjectInArrayByPropertyValue($batteries, 'id', $curentBatteryEntry->battery_id)->name); ?>">
							<i class="fas fa-fire"></i>
						</a>
						<div class="dropdown d-inline-block">
							<button class="btn btn-sm btn-light text-secondary" type="button" data-toggle="dropdown">
								<i class="fas fa-ellipsis-v"></i>
							</button>
							<div class="dropdown-menu">
								<a class="dropdown-item battery-name-cell" data-chore-id="<?php echo e($curentBatteryEntry->battery_id); ?>" type="button" href="#">
									<i class="fas fa-info"></i> <?php echo e($__t('Show battery details')); ?>

								</a>
								<a class="dropdown-item" type="button" href="<?php echo e($U('/batteriesjournal?battery=')); ?><?php echo e($curentBatteryEntry->battery_id); ?>">
									<i class="fas fa-file-alt"></i> <?php echo e($__t('Journal for this battery')); ?>

								</a>
								<a class="dropdown-item" type="button" href="<?php echo e($U('/battery/')); ?><?php echo e($curentBatteryEntry->battery_id); ?>">
									<i class="fas fa-edit"></i> <?php echo e($__t('Edit battery')); ?>

								</a>
							</div>
						</div>
					</td>
					<td class="battery-name-cell cursor-link" data-battery-id="<?php echo e($curentBatteryEntry->battery_id); ?>">
						<?php echo e(FindObjectInArrayByPropertyValue($batteries, 'id', $curentBatteryEntry->battery_id)->name); ?>

					</td>
					<td>
						<span id="battery-<?php echo e($curentBatteryEntry->battery_id); ?>-last-tracked-time"><?php echo e($curentBatteryEntry->last_tracked_time); ?></span>
						<time id="battery-<?php echo e($curentBatteryEntry->battery_id); ?>-last-tracked-time-timeago" class="timeago timeago-contextual" datetime="<?php echo e($curentBatteryEntry->last_tracked_time); ?>"></time>
					</td>
					<td>
						<?php if(FindObjectInArrayByPropertyValue($batteries, 'id', $curentBatteryEntry->battery_id)->charge_interval_days > 0): ?>
							<span id="battery-<?php echo e($curentBatteryEntry->battery_id); ?>-next-charge-time"><?php echo e($curentBatteryEntry->next_estimated_charge_time); ?></span>
							<time id="battery-<?php echo e($curentBatteryEntry->battery_id); ?>-next-charge-time-timeago" class="timeago timeago-contextual" datetime="<?php echo e($curentBatteryEntry->next_estimated_charge_time); ?>"></time>
						<?php else: ?>
							...
						<?php endif; ?>
					</td>
					<td class="d-none">
						"<?php if(FindObjectInArrayByPropertyValue($batteries, 'id', $curentBatteryEntry->battery_id)->charge_interval_days > 0 && $curentBatteryEntry->next_estimated_charge_time < date('Y-m-d H:i:s')): ?> overdue <?php elseif(FindObjectInArrayByPropertyValue($batteries, 'id', $curentBatteryEntry->battery_id)->charge_interval_days > 0 && $curentBatteryEntry->next_estimated_charge_time < date('Y-m-d H:i:s', strtotime("+$nextXDays days"))): ?> duesoon <?php endif; ?>
					</td>

					<?php echo $__env->make('components.userfields_tbody', array(
						'userfields' => $userfields,
						'userfieldValues' => FindAllObjectsInArrayByPropertyValue($userfieldValues, 'object_id', $curentBatteryEntry->battery_id)
					), \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

				</tr>
				<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
			</tbody>
		</table>
	</div>
</div>

<div class="modal fade" id="batteriesoverview-batterycard-modal" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content text-center">
			<div class="modal-body">
				<?php echo $__env->make('components.batterycard', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo e($__t('Close')); ?></button>
			</div>
		</div>
	</div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout.default', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /www/views/batteriesoverview.blade.php ENDPATH**/ ?>