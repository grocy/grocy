<?php if(!GROCY_DISABLE_BROWSER_BARCODE_CAMERA_SCANNING): ?>

<?php $__env->startPush('componentScripts'); ?>
	<script src="<?php echo e($U('/viewjs/components/barcodescanner.js', true)); ?>?v=<?php echo e($version); ?>"></script>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('pageScripts'); ?>
	<script src="<?php echo e($U('/node_modules/quagga/dist/quagga.min.js?v=', true)); ?><?php echo e($version); ?>"></script>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('pageStyles'); ?>
	<style>
		#barcodescanner-start-button {
			position: absolute;
			right: 0;
			margin-top: 4px;
			margin-right: 5px;
			cursor: pointer;
		}

		.combobox-container #barcodescanner-start-button {
			margin-right: 36px !important;
		}
	</style>
<?php $__env->stopPush(); ?>

<?php endif; ?>
<?php /**PATH /www/views/components/barcodescanner.blade.php ENDPATH**/ ?>