<?php $__env->startSection('title', $__t('Tasks')); ?>
<?php $__env->startSection('activeNav', 'tasks'); ?>
<?php $__env->startSection('viewJsName', 'tasks'); ?>

<?php $__env->startPush('pageScripts'); ?>
	<script src="<?php echo e($U('/node_modules/jquery-ui-dist/jquery-ui.min.js?v=', true)); ?><?php echo e($version); ?>"></script>
	<script src="<?php echo e($U('/node_modules/datatables.net-rowgroup/js/dataTables.rowGroup.min.js?v=', true)); ?><?php echo e($version); ?>"></script>
	<script src="<?php echo e($U('/node_modules/datatables.net-rowgroup-bs4/js/rowGroup.bootstrap4.min.js?v=', true)); ?><?php echo e($version); ?>"></script>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('pageStyles'); ?>
	<link href="<?php echo e($U('/node_modules/datatables.net-rowgroup-bs4/css/rowGroup.bootstrap4.min.css?v=', true)); ?><?php echo e($version); ?>" rel="stylesheet">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="row">
	<div class="col">
		<h1>
			<?php echo $__env->yieldContent('title'); ?>
			<a class="btn btn-outline-dark responsive-button" href="<?php echo e($U('/task/new')); ?>">
				<i class="fas fa-plus"></i> <?php echo e($__t('Add')); ?>

			</a>
		</h1>
		<p id="info-due-tasks" data-status-filter="duesoon" data-next-x-days="<?php echo e($nextXDays); ?>" class="btn btn-lg btn-warning status-filter-button responsive-button mr-2"></p>
		<p id="info-overdue-tasks" data-status-filter="overdue" class="btn btn-lg btn-danger status-filter-button responsive-button"></p>
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
	<div class="col-xs-12 col-md-6 col-xl-3 d-flex align-items-end">
		<div class="form-check">
			<input class="form-check-input" type="checkbox" id="show-done-tasks">
			<label class="form-check-label" for="show-done-tasks">
				<?php echo e($__t('Show done tasks')); ?>

			</label>
		</div>
	</div>
</div>

<div class="row">
	<div class="col">
		<table id="tasks-table" class="table table-sm table-striped dt-responsive">
			<thead>
				<tr>
					<th class="border-right"></th>
					<th><?php echo e($__t('Task')); ?></th>
					<th><?php echo e($__t('Due')); ?></th>
					<th class="d-none">Hidden category</th>
					<th><?php echo e($__t('Assigned to')); ?></th>
					<th class="d-none">Hidden status</th>

					<?php echo $__env->make('components.userfields_thead', array(
						'userfields' => $userfields
					), \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

				</tr>
			</thead>
			<tbody class="d-none">
				<?php $__currentLoopData = $tasks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $task): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
				<tr id="task-<?php echo e($task->id); ?>-row" class="<?php if($task->done == 1): ?> text-muted <?php endif; ?> <?php if(!empty($task->due_date) && $task->due_date < date('Y-m-d')): ?> table-danger <?php elseif(!empty($task->due_date) && $task->due_date < date('Y-m-d', strtotime("+$nextXDays days"))): ?> table-warning <?php endif; ?>">
					<td class="fit-content border-right">
						<?php if($task->done == 0): ?>
						<a class="btn btn-success btn-sm do-task-button" href="#" data-toggle="tooltip" data-placement="left" title="<?php echo e($__t('Mark task "%s" as completed', $task->name)); ?>"
							data-task-id="<?php echo e($task->id); ?>"
							data-task-name="<?php echo e($task->name); ?>">
							<i class="fas fa-check"></i>
						</a>
						<?php else: ?>
						<a class="btn btn-secondary btn-sm undo-task-button" href="#" data-toggle="tooltip" data-placement="left" title="<?php echo e($__t('Undo task "%s"', $task->name)); ?>"
							data-task-id="<?php echo e($task->id); ?>"
							data-task-name="<?php echo e($task->name); ?>">
							<i class="fas fa-undo"></i>
						</a>
						<?php endif; ?>
						<a class="btn btn-sm btn-danger delete-task-button" href="#"
							data-task-id="<?php echo e($task->id); ?>"
							data-task-name="<?php echo e($task->name); ?>">
							<i class="fas fa-trash"></i>
						</a>
						<a class="btn btn-info btn-sm" href="<?php echo e($U('/task/')); ?><?php echo e($task->id); ?>">
							<i class="fas fa-edit"></i>
						</a>
					</td>
					<td id="task-<?php echo e($task->id); ?>-name" class="<?php if($task->done == 1): ?> text-strike-through <?php endif; ?>">
						<?php echo e($task->name); ?>

					</td>
					<td>
						<span><?php echo e($task->due_date); ?></span>
						<time class="timeago timeago-contextual" datetime="<?php echo e($task->due_date); ?>"></time>
					</td>
					<td class="d-none">
						<?php if($task->category_id != null): ?> <span><?php echo e(FindObjectInArrayByPropertyValue($taskCategories, 'id', $task->category_id)->name); ?></span> <?php else: ?> <span class="font-italic font-weight-light"><?php echo e($__t('Uncategorized')); ?></span><?php endif; ?>
					</td>
					<td>
						<?php if($task->assigned_to_user_id != null): ?> <span><?php echo e(GetUserDisplayName(FindObjectInArrayByPropertyValue($users, 'id', $task->assigned_to_user_id))); ?></span> <?php endif; ?>
					</td>
					<td class="d-none">
						<?php if($task->done == 1): ?> text-muted <?php endif; ?> <?php if(!empty($task->due_date) && $task->due_date < date('Y-m-d')): ?> overdue <?php elseif(!empty($task->due_date) && $task->due_date < date('Y-m-d', strtotime("+$nextXDays days"))): ?> duesoon <?php endif; ?>
					</td>

					<?php echo $__env->make('components.userfields_tbody', array(
						'userfields' => $userfields,
						'userfieldValues' => FindAllObjectsInArrayByPropertyValue($userfieldValues, 'object_id', $task->id)
					), \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

				</tr>
				<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
			</tbody>
		</table>
	</div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout.default', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /www/views/tasks.blade.php ENDPATH**/ ?>