<?php $__env->startSection('title', $__t('Stock overview')); ?>
<?php $__env->startSection('activeNav', 'stockoverview'); ?>
<?php $__env->startSection('viewJsName', 'stockoverview'); ?>

<?php $__env->startPush('pageScripts'); ?>
	<script src="<?php echo e($U('/node_modules/jquery-ui-dist/jquery-ui.min.js?v=', true)); ?><?php echo e($version); ?>"></script>
	<script src="<?php echo e($U('/viewjs/purchase.js?v=', true)); ?><?php echo e($version); ?>"></script>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('pageStyles'); ?>
	<style>
		.product-name-cell[data-product-has-picture='true'] {
			cursor: pointer;
		}
	</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="row">
	<div class="col">
		<h1><?php echo $__env->yieldContent('title'); ?>
			<small id="info-current-stock" class="text-muted"></small>
			<a class="btn btn-outline-dark responsive-button" href="<?php echo e($U('/stockjournal')); ?>">
				<i class="fas fa-file-alt"></i> <?php echo e($__t('Journal')); ?>

			</a>
			<?php if(GROCY_FEATURE_FLAG_STOCK_LOCATION_TRACKING): ?>
			<a class="btn btn-outline-dark responsive-button" href="<?php echo e($U('/locationcontentsheet')); ?>">
				<i class="fas fa-print"></i> <?php echo e($__t('Location Content Sheet')); ?>

			</a>
			<?php endif; ?>
		</h1>
		<?php if(GROCY_FEATURE_FLAG_STOCK_BEST_BEFORE_DATE_TRACKING): ?>
		<p id="info-expiring-products" data-next-x-days="<?php echo e($nextXDays); ?>" data-status-filter="expiring" class="btn btn-lg btn-warning status-filter-button responsive-button mr-2"></p>
		<p id="info-expired-products" data-status-filter="expired" class="btn btn-lg btn-danger status-filter-button responsive-button mr-2"></p>
		<?php endif; ?>
		<p id="info-missing-products" data-status-filter="belowminstockamount" class="btn btn-lg btn-info status-filter-button responsive-button"></p>
	</div>
</div>

<div class="row mt-3">
	<div class="col-xs-12 col-md-6 col-xl-3">
		<label for="search"><?php echo e($__t('Search')); ?></label> <i class="fas fa-search"></i>
		<input type="text" class="form-control" id="search">
	</div>
	<?php if(GROCY_FEATURE_FLAG_STOCK_LOCATION_TRACKING): ?>
	<div class="col-xs-12 col-md-6 col-xl-3">
		<label for="location-filter"><?php echo e($__t('Filter by location')); ?></label> <i class="fas fa-filter"></i>
		<select class="form-control" id="location-filter">
			<option value="all"><?php echo e($__t('All')); ?></option>
			<?php $__currentLoopData = $locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $location): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
				<option value="<?php echo e($location->name); ?>"><?php echo e($location->name); ?></option>
			<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
		</select>
	</div>
	<?php endif; ?>
	<div class="col-xs-12 col-md-6 col-xl-3">
		<label for="location-filter"><?php echo e($__t('Filter by product group')); ?></label> <i class="fas fa-filter"></i>
		<select class="form-control" id="product-group-filter">
			<option value="all"><?php echo e($__t('All')); ?></option>
			<?php $__currentLoopData = $productGroups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $productGroup): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
				<option value="<?php echo e($productGroup->name); ?>"><?php echo e($productGroup->name); ?></option>
			<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
		</select>
	</div>
	<div class="col-xs-12 col-md-6 col-xl-3">
		<label for="status-filter"><?php echo e($__t('Filter by status')); ?></label> <i class="fas fa-filter"></i>
		<select class="form-control" id="status-filter">
			<option class="bg-white" value="all"><?php echo e($__t('All')); ?></option>
			<?php if(GROCY_FEATURE_FLAG_STOCK_BEST_BEFORE_DATE_TRACKING): ?>
			<option class="bg-warning" value="expiring"><?php echo e($__t('Expiring soon')); ?></option>
			<option class="bg-danger" value="expired"><?php echo e($__t('Already expired')); ?></option>
			<?php endif; ?>
			<option class="bg-info" value="belowminstockamount"><?php echo e($__t('Below min. stock amount')); ?></option>
		</select>
	</div>
</div>

<div class="row">
	<div class="col">
		<table id="stock-overview-table" class="table table-sm table-striped dt-responsive">
			<thead>
				<tr>
					<th class="border-right"></th>
					<th><?php echo e($__t('Product')); ?></th>
					<th><?php echo e($__t('Amount')); ?></th>
					<th><?php echo e($__t('Next best before date')); ?></th>
					<th class="d-none">Hidden location</th>
					<th class="d-none">Hidden status</th>
					<th class="d-none">Hidden product group</th>

					<?php echo $__env->make('components.userfields_thead', array(
						'userfields' => $userfields
					), \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
					
				</tr>
			</thead>
			<tbody class="d-none">
				<?php $__currentLoopData = $currentStock; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $currentStockEntry): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?> 
				<tr id="product-<?php echo e($currentStockEntry->product_id); ?>-row" class="<?php if(GROCY_FEATURE_FLAG_STOCK_BEST_BEFORE_DATE_TRACKING && $currentStockEntry->best_before_date < date('Y-m-d 23:59:59', strtotime('-1 days')) && $currentStockEntry->amount > 0): ?> table-danger <?php elseif(GROCY_FEATURE_FLAG_STOCK_BEST_BEFORE_DATE_TRACKING && $currentStockEntry->best_before_date < date('Y-m-d 23:59:59', strtotime("+$nextXDays days")) && $currentStockEntry->amount > 0): ?> table-warning <?php elseif(FindObjectInArrayByPropertyValue($missingProducts, 'id', $currentStockEntry->product_id) !== null): ?> table-info <?php endif; ?>">
					<td class="fit-content border-right">
						<a class="btn btn-success btn-sm product-consume-button <?php if($currentStockEntry->amount < 1): ?> disabled <?php endif; ?>" href="#" data-toggle="tooltip" data-placement="left" title="<?php echo e($__t('Consume %1$s of %2$s', '1 ' . FindObjectInArrayByPropertyValue($quantityunits, 'id', FindObjectInArrayByPropertyValue($products, 'id', $currentStockEntry->product_id)->qu_id_stock)->name, FindObjectInArrayByPropertyValue($products, 'id', $currentStockEntry->product_id)->name)); ?>"
							data-product-id="<?php echo e($currentStockEntry->product_id); ?>"
							data-product-name="<?php echo e(FindObjectInArrayByPropertyValue($products, 'id', $currentStockEntry->product_id)->name); ?>"
							data-product-qu-name="<?php echo e(FindObjectInArrayByPropertyValue($quantityunits, 'id', FindObjectInArrayByPropertyValue($products, 'id', $currentStockEntry->product_id)->qu_id_stock)->name); ?>"
							data-consume-amount="1">
							<i class="fas fa-utensils"></i> 1
						</a>
						<a id="product-<?php echo e($currentStockEntry->product_id); ?>-consume-all-button" class="btn btn-danger btn-sm product-consume-button <?php if($currentStockEntry->amount == 0): ?> disabled <?php endif; ?>" href="#" data-toggle="tooltip" data-placement="right" title="<?php echo e($__t('Consume all %s which are currently in stock', FindObjectInArrayByPropertyValue($products, 'id', $currentStockEntry->product_id)->name)); ?>"
							data-product-id="<?php echo e($currentStockEntry->product_id); ?>"
							data-product-name="<?php echo e(FindObjectInArrayByPropertyValue($products, 'id', $currentStockEntry->product_id)->name); ?>"
							data-product-qu-name="<?php echo e(FindObjectInArrayByPropertyValue($quantityunits, 'id', FindObjectInArrayByPropertyValue($products, 'id', $currentStockEntry->product_id)->qu_id_stock)->name); ?>"
							data-consume-amount="<?php echo e($currentStockEntry->amount); ?>">
							<i class="fas fa-utensils"></i> <?php echo e($__t('All')); ?>

						</a>
						<?php if(GROCY_FEATURE_FLAG_STOCK_PRODUCT_OPENED_TRACKING): ?>
						<a class="btn btn-success btn-sm product-open-button <?php if($currentStockEntry->amount < 1 || $currentStockEntry->amount == $currentStockEntry->amount_opened): ?> disabled <?php endif; ?>" href="#" data-toggle="tooltip" data-placement="left" title="<?php echo e($__t('Mark %1$s of %2$s as open', '1 ' . FindObjectInArrayByPropertyValue($quantityunits, 'id', FindObjectInArrayByPropertyValue($products, 'id', $currentStockEntry->product_id)->qu_id_stock)->name, FindObjectInArrayByPropertyValue($products, 'id', $currentStockEntry->product_id)->name)); ?>"
							data-product-id="<?php echo e($currentStockEntry->product_id); ?>"
							data-product-name="<?php echo e(FindObjectInArrayByPropertyValue($products, 'id', $currentStockEntry->product_id)->name); ?>"
							data-product-qu-name="<?php echo e(FindObjectInArrayByPropertyValue($quantityunits, 'id', FindObjectInArrayByPropertyValue($products, 'id', $currentStockEntry->product_id)->qu_id_stock)->name); ?>">
							<i class="fas fa-box-open"></i> 1
						</a>
						<?php endif; ?>
						<div class="dropdown d-inline-block">
							<button class="btn btn-sm btn-light text-secondary" type="button" data-toggle="dropdown">
								<i class="fas fa-ellipsis-v"></i>
							</button>
							<div class="dropdown-menu">
								<a class="dropdown-item product-add-to-shopping-list-button" type="button" href="#"
									data-product-id="<?php echo e($currentStockEntry->product_id); ?>">
									<i class="fas fa-shopping-cart"></i> <?php echo e($__t('Add to shopping list')); ?>

								</a>
								<div class="dropdown-divider"></div>
								<a class="dropdown-item product-purchase-button" type="button" href="#"
									data-product-id="<?php echo e($currentStockEntry->product_id); ?>">
									<i class="fas fa-shopping-cart"></i> <?php echo e($__t('Purchase')); ?>

								</a>
								<a class="dropdown-item product-consume-custom-amount-button <?php if($currentStockEntry->amount < 1): ?> disabled <?php endif; ?>" type="button" href="#"
									data-product-id="<?php echo e($currentStockEntry->product_id); ?>">
									<i class="fas fa-utensils"></i> <?php echo e($__t('Consume')); ?>

								</a>
								<a class="dropdown-item product-inventory-button" type="button" href="#"
									data-product-id="<?php echo e($currentStockEntry->product_id); ?>">
									<i class="fas fa-list"></i> <?php echo e($__t('Inventory')); ?>

								</a>
								<div class="dropdown-divider"></div>
								<a class="dropdown-item product-name-cell" data-product-id="<?php echo e($currentStockEntry->product_id); ?>" type="button" href="#">
									<i class="fas fa-info"></i> <?php echo e($__t('Show product details')); ?>

								</a>
								<a class="dropdown-item" type="button" href="<?php echo e($U('/stockjournal?product=')); ?><?php echo e($currentStockEntry->product_id); ?>">
									<i class="fas fa-file-alt"></i> <?php echo e($__t('Stock journal for this product')); ?>

								</a>
								<a class="dropdown-item" type="button" href="<?php echo e($U('/product/')); ?><?php echo e($currentStockEntry->product_id . '?returnto=%2Fstockoverview'); ?>">
									<i class="fas fa-edit"></i> <?php echo e($__t('Edit product')); ?>

								</a>
								<div class="dropdown-divider"></div>
								<a class="dropdown-item product-consume-button product-consume-button-spoiled <?php if($currentStockEntry->amount < 1): ?> disabled <?php endif; ?>" type="button" href="#"
									data-product-id="<?php echo e($currentStockEntry->product_id); ?>"
									data-product-name="<?php echo e(FindObjectInArrayByPropertyValue($products, 'id', $currentStockEntry->product_id)->name); ?>"
									data-product-qu-name="<?php echo e(FindObjectInArrayByPropertyValue($quantityunits, 'id', FindObjectInArrayByPropertyValue($products, 'id', $currentStockEntry->product_id)->qu_id_stock)->name); ?>"
									data-consume-amount="1">
									<i class="fas fa-utensils"></i> <?php echo e($__t('Consume %1$s of %2$s as spoiled', '1 ' . FindObjectInArrayByPropertyValue($quantityunits, 'id', FindObjectInArrayByPropertyValue($products, 'id', $currentStockEntry->product_id)->qu_id_stock)->name, FindObjectInArrayByPropertyValue($products, 'id', $currentStockEntry->product_id)->name)); ?>

								</a>
								<?php if(GROCY_FEATURE_FLAG_RECIPES): ?>
								<a class="dropdown-item" type="button" href="<?php echo e($U('/recipes?search=')); ?><?php echo e(FindObjectInArrayByPropertyValue($products, 'id', $currentStockEntry->product_id)->name); ?>">
									<i class="fas fa-cocktail"></i> <?php echo e($__t('Search for recipes containing this product')); ?>

								</a>
								<?php endif; ?>
							</div>
						</div>
					</td>
					<td class="product-name-cell cursor-link" data-product-id="<?php echo e($currentStockEntry->product_id); ?>">
						<?php echo e(FindObjectInArrayByPropertyValue($products, 'id', $currentStockEntry->product_id)->name); ?>

					</td>
					<td>
						<span id="product-<?php echo e($currentStockEntry->product_id); ?>-amount" class="locale-number locale-number-quantity-amount"><?php echo e($currentStockEntry->amount); ?></span> <span id="product-<?php echo e($currentStockEntry->product_id); ?>-qu-name"><?php echo e($__n($currentStockEntry->amount, FindObjectInArrayByPropertyValue($quantityunits, 'id', FindObjectInArrayByPropertyValue($products, 'id', $currentStockEntry->product_id)->qu_id_stock)->name, FindObjectInArrayByPropertyValue($quantityunits, 'id', FindObjectInArrayByPropertyValue($products, 'id', $currentStockEntry->product_id)->qu_id_stock)->name_plural)); ?></span>
						<span id="product-<?php echo e($currentStockEntry->product_id); ?>-opened-amount" class="small font-italic"><?php if($currentStockEntry->amount_opened > 0): ?><?php echo e($__t('%s opened', $currentStockEntry->amount_opened)); ?><?php endif; ?></span>
						<?php if($currentStockEntry->is_aggregated_amount == 1): ?>
						<span class="pl-1 text-secondary">
							<i class="fas fa-custom-sigma-sign"></i> <span id="product-<?php echo e($currentStockEntry->product_id); ?>-amount-aggregated" class="locale-number locale-number-quantity-amount"><?php echo e($currentStockEntry->amount_aggregated); ?></span> <?php echo e($__n($currentStockEntry->amount_aggregated, FindObjectInArrayByPropertyValue($quantityunits, 'id', FindObjectInArrayByPropertyValue($products, 'id', $currentStockEntry->product_id)->qu_id_stock)->name, FindObjectInArrayByPropertyValue($quantityunits, 'id', FindObjectInArrayByPropertyValue($products, 'id', $currentStockEntry->product_id)->qu_id_stock)->name_plural)); ?>

							<?php if($currentStockEntry->amount_opened_aggregated > 0): ?><span id="product-<?php echo e($currentStockEntry->product_id); ?>-opened-amount-aggregated" class="small font-italic"><?php echo e($__t('%s opened', $currentStockEntry->amount_opened_aggregated)); ?></span><?php endif; ?>
						</span>
						<?php endif; ?>
					</td>
					<td>
						<span id="product-<?php echo e($currentStockEntry->product_id); ?>-next-best-before-date"><?php echo e($currentStockEntry->best_before_date); ?></span>
						<time id="product-<?php echo e($currentStockEntry->product_id); ?>-next-best-before-date-timeago" class="timeago timeago-contextual" datetime="<?php echo e($currentStockEntry->best_before_date); ?> 23:59:59"></time>
					</td>
					<td class="d-none">
						<?php $__currentLoopData = FindAllObjectsInArrayByPropertyValue($currentStockLocations, 'product_id', $currentStockEntry->product_id); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $locationsForProduct): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?> 
						<?php echo e(FindObjectInArrayByPropertyValue($locations, 'id', $locationsForProduct->location_id)->name); ?>

						<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
					</td>
					<td class="d-none">
						<?php if($currentStockEntry->best_before_date < date('Y-m-d 23:59:59', strtotime('-1 days')) && $currentStockEntry->amount > 0): ?> expired <?php elseif($currentStockEntry->best_before_date < date('Y-m-d 23:59:59', strtotime("+$nextXDays days")) && $currentStockEntry->amount > 0): ?> expiring <?php endif; ?> <?php if(FindObjectInArrayByPropertyValue($missingProducts, 'id', $currentStockEntry->product_id) !== null): ?> belowminstockamount <?php endif; ?>
					</td>
					<?php $productGroup = FindObjectInArrayByPropertyValue($productGroups, 'id', FindObjectInArrayByPropertyValue($products, 'id', $currentStockEntry->product_id)->product_group_id) ?>
					<td class="d-none">
						<?php if($productGroup !== null): ?><?php echo e($productGroup->name); ?><?php endif; ?>
					</td>

					<?php echo $__env->make('components.userfields_tbody', array(
						'userfields' => $userfields,
						'userfieldValues' => FindAllObjectsInArrayByPropertyValue($userfieldValues, 'object_id', $currentStockEntry->product_id)
					), \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

				</tr>
				<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
			</tbody>
		</table>
	</div>
</div>

<div class="modal fade" id="stockoverview-productcard-modal" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content text-center">
			<div class="modal-body">
				<?php echo $__env->make('components.productcard', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo e($__t('Close')); ?></button>
			</div>
		</div>
	</div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout.default', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /www/views/stockoverview.blade.php ENDPATH**/ ?>