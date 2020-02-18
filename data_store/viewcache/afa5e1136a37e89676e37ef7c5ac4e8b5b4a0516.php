<?php $__env->startPush('componentScripts'); ?>
	<script src="<?php echo e($U('/viewjs/components/userfieldsform.js', true)); ?>?v=<?php echo e($version); ?>"></script>
<?php $__env->stopPush(); ?>

<?php if(count($userfields) > 0): ?>

<div id="userfields-form" data-entity="<?php echo e($entity); ?>" class="border border-info p-2 mb-2" novalidate>
	<h2 class="small"><?php echo e($__t('Userfields')); ?></h2>

	<?php $__currentLoopData = $userfields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $userfield): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

	<?php if($userfield->type == \Grocy\Services\UserfieldsService::USERFIELD_TYPE_SINGLE_LINE_TEXT): ?>
	<div class="form-group">
		<label for="name"><?php echo e($userfield->caption); ?></label>
		<input type="text" class="form-control userfield-input" data-userfield-name="<?php echo e($userfield->name); ?>">
	</div>
	<?php elseif($userfield->type == \Grocy\Services\UserfieldsService::USERFIELD_TYPE_SINGLE_MULTILINE_TEXT): ?>
	<div class="form-group">
		<label for="description"><?php echo e($userfield->caption); ?></label>
		<textarea class="form-control userfield-input" rows="4" data-userfield-name="<?php echo e($userfield->name); ?>"></textarea>
	</div>
	<?php elseif($userfield->type == \Grocy\Services\UserfieldsService::USERFIELD_TYPE_INTEGRAL_NUMBER): ?>
	<?php echo $__env->make('components.numberpicker', array(
		'id' => $userfield->name,
		'label' => $userfield->caption,
		'noNameAttribute' => true,
		'min' => 0,
		'isRequired' => false,
		'additionalCssClasses' => 'userfield-input',
		'additionalAttributes' => 'data-userfield-name="' . $userfield->name . '"'
	), \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
	<?php elseif($userfield->type == \Grocy\Services\UserfieldsService::USERFIELD_TYPE_DECIMAL_NUMBER): ?>
	<?php echo $__env->make('components.numberpicker', array(
		'id' => '',
		'label' => $userfield->caption,
		'noNameAttribute' => true,
		'min' => 0,
		'step' => 0.01,
		'isRequired' => false,
		'additionalCssClasses' => 'userfield-input',
		'additionalAttributes' => 'data-userfield-name="' . $userfield->name . '"'
	), \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
	<?php elseif($userfield->type == \Grocy\Services\UserfieldsService::USERFIELD_TYPE_DATE): ?>
	<?php echo $__env->make('components.datetimepicker', array(
		'id' => $userfield->name,
		'label' => $userfield->caption,
		'noNameAttribute' => true,
		'format' => 'YYYY-MM-DD',
		'initWithNow' => false,
		'limitEndToNow' => false,
		'limitStartToNow' => false,
		'additionalGroupCssClasses' => 'date-only-datetimepicker',
		'isRequired' => false,
		'additionalCssClasses' => 'userfield-input',
		'additionalAttributes' => 'data-userfield-name="' . $userfield->name . '"'
	), \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
	<?php elseif($userfield->type == \Grocy\Services\UserfieldsService::USERFIELD_TYPE_DATETIME): ?>
	<?php echo $__env->make('components.datetimepicker', array(
		'id' => $userfield->name,
		'label' => $userfield->caption,
		'noNameAttribute' => true,
		'format' => 'YYYY-MM-DD HH:mm:ss',
		'initWithNow' => false,
		'limitEndToNow' => false,
		'limitStartToNow' => false,
		'isRequired' => false,
		'additionalCssClasses' => 'userfield-input',
		'additionalAttributes' => 'data-userfield-name="' . $userfield->name . '"'
	), \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
	<?php elseif($userfield->type == \Grocy\Services\UserfieldsService::USERFIELD_TYPE_CHECKBOX): ?>
	<div class="form-group">
		<div class="form-check">
			<input class="form-check-input userfield-input" type="checkbox" data-userfield-name="<?php echo e($userfield->name); ?>" value="1">
			<label class="form-check-label" for="<?php echo e($userfield->name); ?>"><?php echo e($userfield->caption); ?></label>
		</div>
	</div>
	<?php elseif($userfield->type == \Grocy\Services\UserfieldsService::USERFIELD_TYPE_PRESET_LIST): ?>
	<div class="form-group">
		<label for="<?php echo e($userfield->name); ?>"><?php echo e($userfield->caption); ?></label>
		<select class="form-control userfield-input" data-userfield-name="<?php echo e($userfield->name); ?>">
			<option></option>
			<?php $__currentLoopData = preg_split('/\r\n|\r|\n/', $userfield->config); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
				<option value="<?php echo e($option); ?>"><?php echo e($option); ?></option>
			<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
		</select>
	</div>
	<?php elseif($userfield->type == \Grocy\Services\UserfieldsService::USERFIELD_TYPE_PRESET_CHECKLIST): ?>
	<div class="form-group">
		<label for="<?php echo e($userfield->name); ?>"><?php echo e($userfield->caption); ?></label>
		<select multiple class="form-control userfield-input selectpicker" data-userfield-name="<?php echo e($userfield->name); ?>" data-actions-Box="true" data-live-search="true">
			<?php $__currentLoopData = preg_split('/\r\n|\r|\n/', $userfield->config); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
				<option value="<?php echo e($option); ?>"><?php echo e($option); ?></option>
			<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
		</select>
	</div>
	<?php elseif($userfield->type == \Grocy\Services\UserfieldsService::USERFIELD_TYPE_LINK): ?>
	<div class="form-group">
		<label for="name"><?php echo e($userfield->caption); ?></label>
		<input type="link" class="form-control userfield-input" data-userfield-name="<?php echo e($userfield->name); ?>">
	</div>
	<?php endif; ?>

	<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

</div>

<?php endif; ?>
<?php /**PATH /www/views/components/userfieldsform.blade.php ENDPATH**/ ?>