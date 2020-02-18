<?php if(count($userfields) > 0): ?>

<?php $__currentLoopData = $userfields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $userfield): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

<?php if($userfield->show_as_column_in_tables == 1): ?>
	<?php $userfieldObject = FindObjectInArrayByPropertyValue($userfieldValues, 'name', $userfield->name) ?>
	<td>
	<?php if($userfieldObject !== null): ?>
		<?php if($userfield->type == \Grocy\Services\UserfieldsService::USERFIELD_TYPE_CHECKBOX): ?>
			<?php if($userfieldObject->value == 1): ?><i class="fas fa-check"></i><?php endif; ?>
		<?php elseif($userfield->type == \Grocy\Services\UserfieldsService::USERFIELD_TYPE_PRESET_CHECKLIST): ?>
			<?php echo str_replace(',', '<br>', $userfieldObject->value); ?>

		<?php elseif($userfield->type == \Grocy\Services\UserfieldsService::USERFIELD_TYPE_LINK): ?>
			<a href="<?php echo e($userfieldObject->value); ?>" target="_blank"><?php echo e($userfieldObject->value); ?></a>
		<?php else: ?>
			<?php echo e($userfieldObject->value); ?>

		<?php endif; ?>
	<?php endif; ?>
	</td>
<?php endif; ?>

<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

<?php endif; ?>
<?php /**PATH /www/views/components/userfields_tbody.blade.php ENDPATH**/ ?>