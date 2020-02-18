<?php $__env->startPush('componentScripts'); ?>
	<script src="<?php echo e($U('/viewjs/components/recipepicker.js', true)); ?>?v=<?php echo e($version); ?>"></script>
<?php $__env->stopPush(); ?>

<?php if(empty($prefillByName)) { $prefillByName = ''; } ?>
<?php if(empty($prefillById)) { $prefillById = ''; } ?>
<?php if(!isset($isRequired)) { $isRequired = true; } ?>
<?php if(empty($hint)) { $hint = ''; } ?>
<?php if(empty($hintId)) { $hintId = ''; } ?>
<?php if(empty($nextInputSelector)) { $nextInputSelector = ''; } ?>

<div class="form-group" data-next-input-selector="<?php echo e($nextInputSelector); ?>" data-prefill-by-name="<?php echo e($prefillByName); ?>" data-prefill-by-id="<?php echo e($prefillById); ?>">
	<label for="recipe_id"><?php echo e($__t('Recipe')); ?>&nbsp;&nbsp;<span id="<?php echo e($hintId); ?>" class="small text-muted"><?php echo e($hint); ?></span></label>
	<select class="form-control recipe-combobox" id="recipe_id" name="recipe_id" <?php if($isRequired): ?> required <?php endif; ?>>
		<option value=""></option>
		<?php $__currentLoopData = $recipes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $recipe): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
			<option value="<?php echo e($recipe->id); ?>"><?php echo e($recipe->name); ?></option>
		<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
	</select>
	<div class="invalid-feedback"><?php echo e($__t('You have to select a recipe')); ?></div>
</div>
<?php /**PATH /www/views/components/recipepicker.blade.php ENDPATH**/ ?>