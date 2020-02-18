<?php $__env->startPush('componentScripts'); ?>
	<script src="<?php echo e($U('/node_modules/chart.js/dist/Chart.min.js?v=', true)); ?><?php echo e($version); ?>"></script>
	<script src="<?php echo e($U('/viewjs/components/productcard.js', true)); ?>?v=<?php echo e($version); ?>"></script>
<?php $__env->stopPush(); ?>

<div class="card">
	<div class="card-header">
		<i class="fab fa-product-hunt"></i> <?php echo e($__t('Product overview')); ?>

		<a id="productcard-product-edit-button" class="btn btn-sm btn-outline-info py-0 float-right disabled" href="#" data-toggle="tooltip" title="<?php echo e($__t('Edit product')); ?>">
			<i class="fas fa-edit"></i>
		</a>
		<a id="productcard-product-journal-button" class="btn btn-sm btn-outline-secondary py-0 mr-2 float-right disabled show-as-dialog-link" href="#" data-toggle="tooltip" title="<?php echo e($__t('Stock journal for this product')); ?>">
			<i class="fas fa-file-alt"></i>
		</a>
	</div>
	<div class="card-body">
		<h3><span id="productcard-product-name"></span></h3>

		<div id="productcard-product-description-wrapper" class="expandable-text mb-2 d-none">
			<p id="productcard-product-description" class="text-muted collapse mb-0"></p>
			<a class="collapsed" data-toggle="collapse" href="#productcard-product-description"><?php echo e($__t('Show more')); ?></a>
		</div>

		<strong><?php echo e($__t('Stock amount') . ' / ' . $__t('Quantity unit')); ?>:</strong> <span id="productcard-product-stock-amount" class="locale-number locale-number-quantity-amount"></span> <span id="productcard-product-stock-qu-name"></span> <span id="productcard-product-stock-opened-amount" class="small font-italic locale-number locale-number-quantity-amount"></span>
		<span id="productcard-aggregated-amounts" class="pl-2 text-secondary d-none"><i class="fas fa-custom-sigma-sign"></i> <span id="productcard-product-stock-amount-aggregated" class="locale-number locale-number-quantity-amount"></span> <span id="productcard-product-stock-qu-name-aggregated"></span> <span id="productcard-product-stock-opened-amount-aggregated locale-number locale-number-quantity-amount" class="small font-italic"></span></span><br>
		<?php if(GROCY_FEATURE_FLAG_STOCK_LOCATION_TRACKING): ?><strong><?php echo e($__t('Location')); ?>:</strong> <span id="productcard-product-location"></span><br><?php endif; ?>
		<strong><?php echo e($__t('Last purchased')); ?>:</strong> <span id="productcard-product-last-purchased"></span> <time id="productcard-product-last-purchased-timeago" class="timeago timeago-contextual"></time><br>
		<strong><?php echo e($__t('Last used')); ?>:</strong> <span id="productcard-product-last-used"></span> <time id="productcard-product-last-used-timeago" class="timeago timeago-contextual"></time><br>
		<?php if(GROCY_FEATURE_FLAG_STOCK_PRICE_TRACKING): ?><strong><?php echo e($__t('Last price')); ?>:</strong> <span id="productcard-product-last-price"></span><br><?php endif; ?>
		<?php if(GROCY_FEATURE_FLAG_STOCK_BEST_BEFORE_DATE_TRACKING): ?><strong><?php echo e($__t('Average shelf life')); ?>:</strong> <span id="productcard-product-average-shelf-life"></span><br><?php endif; ?>
		<strong><?php echo e($__t('Spoil rate')); ?>:</strong> <span id="productcard-product-spoil-rate"></span>

		<p class="w-75 mt-3 mx-auto"><img id="productcard-product-picture" data-src="" class="img-fluid img-thumbnail d-none lazy"></p>

		<?php if(GROCY_FEATURE_FLAG_STOCK_PRICE_TRACKING): ?>
		<h5 class="mt-3"><?php echo e($__t('Price history')); ?></h5>
		<canvas id="productcard-product-price-history-chart" class="w-100 d-none"></canvas>
		<span id="productcard-no-price-data-hint" class="font-italic d-none"><?php echo e($__t('No price history available')); ?></span>
		<?php endif; ?>
	</div>
</div>
<?php /**PATH /www/views/components/productcard.blade.php ENDPATH**/ ?>