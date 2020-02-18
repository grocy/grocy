<?php if(count($userfields) > 0): ?>

<?php $__currentLoopData = $userfields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $userfield): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

<?php if($userfield->show_as_column_in_tables == 1): ?>
	<th><?php echo e($userfield->caption); ?></th>
<?php endif; ?>

<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

<?php endif; ?>
<?php /**PATH /www/views/components/userfields_thead.blade.php ENDPATH**/ ?>