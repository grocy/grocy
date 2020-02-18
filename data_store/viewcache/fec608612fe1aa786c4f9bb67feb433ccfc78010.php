<?php $__env->startPush('componentScripts'); ?>
	<script src="<?php echo e($U('/viewjs/components/calendarcard.js', true)); ?>?v=<?php echo e($version); ?>"></script>
<?php $__env->stopPush(); ?>

<div class="card">
	<div class="card-header">
		<i class="fas fa-calendar"></i> <?php echo e($__t('Calendar')); ?>

	</div>
	<div class="card-body">
		<div id="calendar" data-target-input="nearest"></div>
	</div>
</div>
<?php /**PATH /www/views/components/calendarcard.blade.php ENDPATH**/ ?>