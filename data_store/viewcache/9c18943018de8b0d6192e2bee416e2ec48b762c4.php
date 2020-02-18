<?php $__env->startPush('componentScripts'); ?>
	<script src="<?php echo e($U('/viewjs/components/locationpicker.js', true)); ?>?v=<?php echo e($version); ?>"></script>
<?php $__env->stopPush(); ?>

<?php if(empty($prefillByName)) { $prefillByName = ''; } ?>
<?php if(empty($prefillById)) { $prefillById = ''; } ?>
<?php if(!isset($isRequired)) { $isRequired = true; } ?>
<?php if(empty($hint)) { $hint = ''; } ?>

<div class="form-group" data-next-input-selector="<?php echo e($nextInputSelector); ?>" data-prefill-by-name="<?php echo e($prefillByName); ?>" data-prefill-by-id="<?php echo e($prefillById); ?>">
	<label for="location_id"><?php echo e($__t('Location')); ?>&nbsp;&nbsp;<span id="<?php echo e($hintId); ?>" class="small text-muted"><?php echo e($hint); ?></span></label>
	<select class="form-control location-combobox" id="location_id" name="location_id" <?php if($isRequired): ?> required <?php endif; ?>>
		<option value=""></option>
		<?php $__currentLoopData = $locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $location): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
			<option value="<?php echo e($location->id); ?>"><?php echo e($location->name); ?></option>
		<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
	</select>
	<div class="invalid-feedback"><?php echo e($__t('You have to select a location')); ?></div>
</div>
<?php /**PATH /www/views/components/locationpicker.blade.php ENDPATH**/ ?>