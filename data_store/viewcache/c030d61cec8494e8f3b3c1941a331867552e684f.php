<?php $__env->startPush('componentScripts'); ?>
	<script src="<?php echo e($U('/viewjs/components/productpicker.js', true)); ?>?v=<?php echo e($version); ?>"></script>
<?php $__env->stopPush(); ?>

<?php if(empty($disallowAddProductWorkflows)) { $disallowAddProductWorkflows = false; } ?>
<?php if(empty($disallowAllProductWorkflows)) { $disallowAllProductWorkflows = false; } ?>
<?php if(empty($prefillByName)) { $prefillByName = ''; } ?>
<?php if(empty($prefillById)) { $prefillById = ''; } ?>
<?php if(!isset($isRequired)) { $isRequired = true; } ?>
<?php if(!isset($label)) { $label = 'Product'; } ?>
<?php if(!isset($disabled)) { $disabled = false; } ?>
<?php if(empty($hint)) { $hint = ''; } ?>
<?php if(empty($nextInputSelector)) { $nextInputSelector = ''; } ?>

<div class="form-group" data-next-input-selector="<?php echo e($nextInputSelector); ?>" data-disallow-add-product-workflows="<?php echo e(BoolToString($disallowAddProductWorkflows)); ?>" data-disallow-all-product-workflows="<?php echo e(BoolToString($disallowAllProductWorkflows)); ?>" data-prefill-by-name="<?php echo e($prefillByName); ?>" data-prefill-by-id="<?php echo e($prefillById); ?>">
	<label for="product_id"><?php echo e($__t($label)); ?> <i class="fas fa-barcode"></i><span id="barcode-lookup-disabled-hint" class="small text-muted d-none"> <?php echo e($__t('Barcode lookup is disabled')); ?></span>&nbsp;&nbsp;<span class="small text-muted"><?php echo e($hint); ?></span></label>
	<select class="form-control product-combobox barcodescanner-input" id="product_id" name="product_id" <?php if($isRequired): ?> required <?php endif; ?> <?php if($disabled): ?> disabled <?php endif; ?>>
		<option value=""></option>
		<?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
			<option data-additional-searchdata="<?php echo e($product->barcode); ?><?php if(!empty($product->barcode)): ?>,<?php endif; ?>" value="<?php echo e($product->id); ?>"><?php echo e($product->name); ?></option>
		<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
	</select>
	<div class="invalid-feedback"><?php echo e($__t('You have to select a product')); ?></div>
	<div id="custom-productpicker-error" class="form-text text-danger d-none"></div>
	<?php if(!$disallowAllProductWorkflows): ?>
		<div class="form-text text-info small"><?php echo e($__t('Type a new product name or barcode and hit TAB to start a workflow')); ?></div>
	<?php endif; ?>
	<div id="flow-info-addbarcodetoselection" class="form-text text-muted small d-none"><strong><span id="addbarcodetoselection"></span></strong> <?php echo e($__t('will be added to the list of barcodes for the selected product on submit')); ?></div>
</div>

<?php echo $__env->make('components.barcodescanner', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php /**PATH /www/views/components/productpicker.blade.php ENDPATH**/ ?>