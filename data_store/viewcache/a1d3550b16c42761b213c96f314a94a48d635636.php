<?php $__env->startSection('title', $__t('Purchase')); ?>
<?php $__env->startSection('activeNav', 'purchase'); ?>
<?php $__env->startSection('viewJsName', 'purchase'); ?>

<?php $__env->startSection('content'); ?>
<div class="row">
	<div class="col-xs-12 col-md-6 col-xl-4 pb-3">
		<h1><?php echo $__env->yieldContent('title'); ?></h1>

		<form id="purchase-form" novalidate>

			<?php echo $__env->make('components.productpicker', array(
				'products' => $products,
				'nextInputSelector' => '#best_before_date .datetimepicker-input'
			), \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

			<?php
				$additionalGroupCssClasses = '';
				if (!GROCY_FEATURE_FLAG_STOCK_BEST_BEFORE_DATE_TRACKING)
				{
					$additionalGroupCssClasses = 'd-none';
				}
			?>
			<?php echo $__env->make('components.datetimepicker', array(
				'id' => 'best_before_date',
				'label' => 'Best before',
				'format' => 'YYYY-MM-DD',
				'initWithNow' => false,
				'limitEndToNow' => false,
				'limitStartToNow' => false,
				'invalidFeedback' => $__t('A best before date is required'),
				'nextInputSelector' => '#amount',
				'additionalCssClasses' => 'date-only-datetimepicker',
				'shortcutValue' => '2999-12-31',
				'shortcutLabel' => 'Never expires',
				'earlierThanInfoLimit' => date('Y-m-d'),
				'earlierThanInfoText' => $__t('The given date is earlier than today, are you sure?'),
				'additionalGroupCssClasses' => $additionalGroupCssClasses
			), \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
			<?php $additionalGroupCssClasses = ''; ?>

			<?php echo $__env->make('components.numberpicker', array(
				'id' => 'amount',
				'label' => 'Amount',
				'hintId' => 'amount_qu_unit',
				'min' => 1,
				'invalidFeedback' => $__t('The amount cannot be lower than %s', '1'),
				'additionalHtmlContextHelp' => '<div id="tare-weight-handling-info" class="text-info font-italic d-none">' . $__t('Tare weight handling enabled - please weigh the whole container, the amount to be posted will be automatically calculcated') . '</div>'
			), \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

			<?php if(GROCY_FEATURE_FLAG_STOCK_PRICE_TRACKING): ?>
			<?php echo $__env->make('components.numberpicker', array(
				'id' => 'price',
				'label' => 'Price',
				'min' => 0,
				'step' => 0.0001,
				'value' => '',
				'hint' => $__t('in %s and based on the purchase quantity unit', GROCY_CURRENCY),
				'invalidFeedback' => $__t('The price cannot be lower than %s', '0'),
				'isRequired' => false,
				'additionalGroupCssClasses' => 'mb-1'
			), \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
			<div class="form-check form-check-inline mb-3">
				<input class="form-check-input" type="radio" name="price-type" id="price-type-unit-price" value="unit-price" checked>
				<label class="form-check-label" for="price-type-unit-price"><?php echo e($__t('Unit price')); ?></label>
			</div>
			<div class="form-check form-check-inline mb-3">
				<input class="form-check-input" type="radio" name="price-type" id="price-type-total-price" value="total-price">
				<label class="form-check-label" for="price-type-total-price"><?php echo e($__t('Total price')); ?></label>
			</div>
			<?php else: ?>
			<input type="hidden" name="price" id="price" value="0">
			<?php endif; ?>

			<?php if(GROCY_FEATURE_FLAG_STOCK_LOCATION_TRACKING): ?>
			<?php echo $__env->make('components.locationpicker', array(
				'locations' => $locations,
				'isRequired' => false
			), \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
			<?php else: ?>
			<input type="hidden" name="location_id" id="location_id" value="1">
			<?php endif; ?>

			<button id="save-purchase-button" class="btn btn-success"><?php echo e($__t('OK')); ?></button>

		</form>
	</div>

	<div class="col-xs-12 col-md-6 col-xl-4 hide-when-embedded">
		<?php echo $__env->make('components.productcard', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
	</div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout.default', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /www/views/purchase.blade.php ENDPATH**/ ?>