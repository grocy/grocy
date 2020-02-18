<?php $__env->startPush('componentScripts'); ?>
	<script src="<?php echo e($U('/viewjs/components/numberpicker.js', true)); ?>?v=<?php echo e($version); ?>"></script>
<?php $__env->stopPush(); ?>

<?php if(!isset($value)) { $value = 1; } ?>
<?php if(empty($min)) { $min = 0; } ?>
<?php if(empty($max)) { $max = 999999; } ?>
<?php if(empty($step)) { $step = 1; } ?>
<?php if(empty($hint)) { $hint = ''; } ?>
<?php if(empty($hintId)) { $hintId = ''; } ?>
<?php if(empty($additionalCssClasses)) { $additionalCssClasses = ''; } ?>
<?php if(empty($additionalGroupCssClasses)) { $additionalGroupCssClasses = ''; } ?>
<?php if(empty($additionalAttributes)) { $additionalAttributes = ''; } ?>
<?php if(empty($additionalHtmlElements)) { $additionalHtmlElements = ''; } ?>
<?php if(empty($additionalHtmlContextHelp)) { $additionalHtmlContextHelp = ''; } ?>
<?php if(!isset($isRequired)) { $isRequired = true; } ?>
<?php if(!isset($noNameAttribute)) { $noNameAttribute = false; } ?>

<div class="form-group <?php echo e($additionalGroupCssClasses); ?>">
	<label for="<?php echo e($id); ?>"><?php echo e($__t($label)); ?>&nbsp;&nbsp;<span id="<?php echo e($hintId); ?>" class="small text-muted"><?php echo e($hint); ?></span><?php echo $additionalHtmlContextHelp; ?></label>
	<div class="input-group">
		<input <?php echo $additionalAttributes; ?> type="number" class="form-control numberpicker <?php echo e($additionalCssClasses); ?>" id="<?php echo e($id); ?>" <?php if(!$noNameAttribute): ?> name="<?php echo e($id); ?>" <?php endif; ?> value="<?php echo e($value); ?>" min="<?php echo e($min); ?>" max="<?php echo e($max); ?>" step="<?php echo e($step); ?>" <?php if($isRequired): ?> required <?php endif; ?>>
		<div class="input-group-append">
			<div class="input-group-text numberpicker-up-button"><i class="fas fa-arrow-up"></i></div>
		</div>
		<div class="input-group-append">
			<div class="input-group-text numberpicker-down-button"><i class="fas fa-arrow-down"></i></div>
		</div>
		<div class="invalid-feedback"><?php echo e($invalidFeedback); ?></div>
	</div>
	<?php echo $additionalHtmlElements; ?>

</div>
<?php /**PATH /www/views/components/numberpicker.blade.php ENDPATH**/ ?>