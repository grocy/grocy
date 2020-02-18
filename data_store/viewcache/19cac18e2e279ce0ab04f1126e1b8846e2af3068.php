<?php $__env->startSection('title', $__t('Shopping list')); ?>
<?php $__env->startSection('activeNav', 'shoppinglist'); ?>
<?php $__env->startSection('viewJsName', 'shoppinglist'); ?>

<?php $__env->startPush('pageScripts'); ?>
	<script src="<?php echo e($U('/node_modules/jquery-ui-dist/jquery-ui.min.js?v=', true)); ?><?php echo e($version); ?>"></script>
	<script src="<?php echo e($U('/node_modules/datatables.net-rowgroup/js/dataTables.rowGroup.min.js?v=', true)); ?><?php echo e($version); ?>"></script>
	<script src="<?php echo e($U('/node_modules/datatables.net-rowgroup-bs4/js/rowGroup.bootstrap4.min.js?v=', true)); ?><?php echo e($version); ?>"></script>
	<script src="<?php echo e($U('/viewjs/purchase.js?v=', true)); ?><?php echo e($version); ?>"></script>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('pageStyles'); ?>
	<link href="<?php echo e($U('/node_modules/datatables.net-rowgroup-bs4/css/rowGroup.bootstrap4.min.css?v=', true)); ?><?php echo e($version); ?>" rel="stylesheet">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<?php if(GROCY_FEATURE_FLAG_SHOPPINGLIST_MULTIPLE_LISTS): ?>
<div class="row border-bottom pb-2 mb-2 d-print-none">
	<div class="col-xs-12 col-md-4">
		<label for="selected-shopping-list"><?php echo e($__t('Selected shopping list')); ?></label>
		<select class="form-control" id="selected-shopping-list">
			<?php $__currentLoopData = $shoppingLists; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $shoppingList): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
			<option <?php if($shoppingList->id == $selectedShoppingListId): ?> selected="selected" <?php endif; ?> value="<?php echo e($shoppingList->id); ?>"><?php echo e($shoppingList->name); ?></option>
			<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
		</select>
	</div>
	<div class="col-xs-12 col-md-8">
		<label for="selected-shopping-list">&nbsp;</label><br>
		<a class="btn btn-outline-dark responsive-button" href="<?php echo e($U('/shoppinglist/new')); ?>">
			<i class="fas fa-plus"></i> <?php echo e($__t('New shopping list')); ?>

		</a>
		<a id="delete-selected-shopping-list" class="btn btn-outline-danger responsive-button <?php if($selectedShoppingListId == 1): ?> disabled <?php endif; ?>" href="#">
			<i class="fas fa-trash"></i> <?php echo e($__t('Delete shopping list')); ?>

		</a>
		<a id="print-shopping-list-button" class="btn btn-outline-dark responsive-button" href="#">
			<i class="fas fa-print"></i> <?php echo e($__t('Print')); ?>

		</a>
		<!--<div class="dropdown d-inline-block">
			<button class="btn btn-outline-dark responsive-button dropdown-toggle" data-toggle="dropdown"><i class="fas fa-file-export"></i> <?php echo e($__t('Output')); ?></button>
			<div class="dropdown-menu">
				<a id="print-shopping-list-button" class="dropdown-item" href="#"><i class="fas fa-print"></i> <?php echo e($__t('Print')); ?></a>
			</div>
		</div>-->
	</div>
</div>
<?php endif; ?>

<div class="row d-print-none">
	<div class="col">
		<h1>
			<?php echo $__env->yieldContent('title'); ?>
			<a class="btn btn-outline-dark responsive-button" href="<?php echo e($U('/shoppinglistitem/new?list=' . $selectedShoppingListId)); ?>">
				<i class="fas fa-plus"></i> <?php echo e($__t('Add item')); ?>

			</a>
			<a id="clear-shopping-list" class="btn btn-outline-danger responsive-button <?php if($listItems->count() == 0): ?> disabled <?php endif; ?>" href="#">
				<i class="fas fa-trash"></i> <?php echo e($__t('Clear list')); ?>

			</a>
			<a id="add-products-below-min-stock-amount" class="btn btn-outline-primary responsive-button" href="#">
				<i class="fas fa-cart-plus"></i> <?php echo e($__t('Add products that are below defined min. stock amount')); ?>

			</a>
			<a id="add-all-items-to-stock-button" class="btn btn-outline-primary responsive-button" href="#">
				<i class="fas fa-box"></i> <?php echo e($__t('Add all list items to stock')); ?>

			</a>
		</h1>
		<p data-status-filter="belowminstockamount" class="btn btn-lg btn-info status-filter-button responsive-button"><?php echo e($__n(count($missingProducts), '%s product is below defined min. stock amount', '%s products are below defined min. stock amount')); ?></p>
	</div>
</div>

<div class="row mt-3 d-print-none">
	<div class="col-xs-12 col-md-4">
		<label for="search"><?php echo e($__t('Search')); ?></label> <i class="fas fa-search"></i>
		<input type="text" class="form-control" id="search">
	</div>
	<div class="col-xs-12 col-md-4">
		<label for="status-filter"><?php echo e($__t('Filter by status')); ?></label> <i class="fas fa-filter"></i>
		<select class="form-control" id="status-filter">
			<option class="bg-white" value="all"><?php echo e($__t('All')); ?></option>
			<option class="bg-info" value="belowminstockamount"><?php echo e($__t('Below min. stock amount')); ?></option>
		</select>
	</div>
</div>

<div class="row d-print-none">
	<div class="col-xs-12 col-md-8 pb-3">
		<table id="shoppinglist-table" class="table table-sm table-striped dt-responsive">
			<thead>
				<tr>
					<th class="border-right"></th>
					<th><?php echo e($__t('Product')); ?> / <em><?php echo e($__t('Note')); ?></em></th>
					<th><?php echo e($__t('Amount')); ?></th>
					<th class="d-none">Hiden product group</th>
					<th class="d-none">Hidden status</th>

					<?php echo $__env->make('components.userfields_thead', array(
						'userfields' => $userfields
					), \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

				</tr>
			</thead>
			<tbody class="d-none">
				<?php $__currentLoopData = $listItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $listItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
				<tr id="shoppinglistitem-<?php echo e($listItem->id); ?>-row" class="<?php if(FindObjectInArrayByPropertyValue($missingProducts, 'id', $listItem->product_id) !== null): ?> table-info <?php endif; ?> <?php if($listItem->done == 1): ?> text-muted text-strike-through <?php endif; ?>">
					<td class="fit-content border-right">
						<a class="btn btn-success btn-sm order-listitem-button" href="#"
							data-item-id="<?php echo e($listItem->id); ?>"
							data-item-done="<?php echo e($listItem->done); ?>">
							<i class="fas fa-check"></i>
						</a>
						<a class="btn btn-sm btn-info" href="<?php echo e($U('/shoppinglistitem/') . $listItem->id . '?list=' . $selectedShoppingListId); ?>">
							<i class="fas fa-edit"></i>
						</a>
						<a class="btn btn-sm btn-danger shoppinglist-delete-button" href="#" data-shoppinglist-id="<?php echo e($listItem->id); ?>">
							<i class="fas fa-trash"></i>
						</a>
						<a class="btn btn-sm btn-primary <?php if(empty($listItem->product_id)): ?> disabled <?php else: ?> shopping-list-stock-add-workflow-list-item-button <?php endif; ?>" href="<?php echo e($U('/purchase?embedded&flow=shoppinglistitemtostock&product=')); ?><?php echo e($listItem->product_id); ?>&amount=<?php echo e($listItem->amount); ?>&listitemid=<?php echo e($listItem->id); ?>" <?php if(!empty($listItem->product_id)): ?> data-toggle="tooltip" title="<?php echo e($__t('Add %1$s of %2$s to stock', $listItem->amount . ' ' . $__n($listItem->amount, FindObjectInArrayByPropertyValue($quantityunits, 'id', FindObjectInArrayByPropertyValue($products, 'id', $listItem->product_id)->qu_id_purchase)->name, FindObjectInArrayByPropertyValue($quantityunits, 'id', FindObjectInArrayByPropertyValue($products, 'id', $listItem->product_id)->qu_id_purchase)->name_plural), FindObjectInArrayByPropertyValue($products, 'id', $listItem->product_id)->name, $listItem->amount)); ?>" <?php endif; ?>>
							<i class="fas fa-box"></i>
						</a>
					</td>
					<td>
						<?php if(!empty($listItem->product_id)): ?> <?php echo e(FindObjectInArrayByPropertyValue($products, 'id', $listItem->product_id)->name); ?><br><?php endif; ?><em><?php echo nl2br($listItem->note); ?></em>
					</td>
					<td>
						<span class="locale-number locale-number-quantity-amount"><?php echo e($listItem->amount); ?></span> <?php if(!empty($listItem->product_id)): ?><?php echo e($__n($listItem->amount, FindObjectInArrayByPropertyValue($quantityunits, 'id', FindObjectInArrayByPropertyValue($products, 'id', $listItem->product_id)->qu_id_purchase)->name, FindObjectInArrayByPropertyValue($quantityunits, 'id', FindObjectInArrayByPropertyValue($products, 'id', $listItem->product_id)->qu_id_purchase)->name_plural)); ?><?php endif; ?>
					</td>
					<td class="d-none">
						<?php if(!empty(FindObjectInArrayByPropertyValue($products, 'id', $listItem->product_id)->product_group_id)): ?> <?php echo e(FindObjectInArrayByPropertyValue($productGroups, 'id', FindObjectInArrayByPropertyValue($products, 'id', $listItem->product_id)->product_group_id)->name); ?> <?php else: ?> <span class="font-italic font-weight-light"><?php echo e($__t('Ungrouped')); ?></span> <?php endif; ?>
					</td>
					<td class="d-none">
						<?php if(FindObjectInArrayByPropertyValue($missingProducts, 'id', $listItem->product_id) !== null): ?> belowminstockamount <?php endif; ?>
					</td>

					<?php echo $__env->make('components.userfields_tbody', array(
						'userfields' => $userfields,
						'userfieldValues' => FindAllObjectsInArrayByPropertyValue($userfieldValues, 'object_id', $listItem->product_id)
					), \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

				</tr>
				<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
			</tbody>
		</table>
	</div>

	<div class="col-xs-12 col-md-4 mt-md-2 d-print-none">
		<?php echo $__env->make('components.calendarcard', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
	</div>
</div>

<div class="row mt-3 d-print-none">
	<div class="col-xs-12 col-md-8">
		<div class="form-group">
			<label class="text-larger font-weight-bold" for="notes"><?php echo e($__t('Notes')); ?></label>
			<a id="save-description-button" class="btn btn-success btn-sm ml-1 mb-2" href="#"><?php echo e($__t('Save')); ?></a>
			<a id="clear-description-button" class="btn btn-danger btn-sm ml-1 mb-2" href="#"><?php echo e($__t('Clear')); ?></a>
			<textarea class="form-control wysiwyg-editor" id="description" name="description"><?php echo e(FindObjectInArrayByPropertyValue($shoppingLists, 'id', $selectedShoppingListId)->description); ?></textarea>
		</div>
	</div>
</div>

<div class="modal fade" id="shopping-list-stock-add-workflow-modal" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content text-center">
			<div class="modal-body">
				<iframe id="shopping-list-stock-add-workflow-purchase-form-frame" class="embed-responsive" src=""></iframe>
			</div>
			<div class="modal-footer">
				<span id="shopping-list-stock-add-workflow-purchase-item-count" class="d-none mr-auto"></span>
				<button id="shopping-list-stock-add-workflow-skip-button" type="button" class="btn btn-primary"><i class="fas fa-angle-double-right"></i> <?php echo e($__t('Skip')); ?></button>
				<button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo e($__t('Close')); ?></button>
			</div>
		</div>
	</div>
</div>

<div class="d-none d-print-block">
	<h1 class="text-center">
		<img src="<?php echo e($U('/img/grocy_logo.svg?v=', true)); ?><?php echo e($version); ?>" height="30" class="d-print-flex mx-auto">
		<?php echo e($__t("Shopping list")); ?>

	</h1>
	<?php if(FindObjectInArrayByPropertyValue($shoppingLists, 'id', $selectedShoppingListId)->name != $__t("Shopping list")): ?>
	<h3 class="text-center">
		<?php echo e(FindObjectInArrayByPropertyValue($shoppingLists, 'id', $selectedShoppingListId)->name); ?>

	</h3>
	<?php endif; ?>
	<h6 class="text-center mb-4">
		<?php echo e($__t('Time of printing')); ?>:
		<span class="d-inline print-timestamp"></span>
	</h6>
	<div class="row w-75">
		<div class="col">
			<table class="table">
				<thead>
					<tr>
						<th><?php echo e($__t('Product')); ?> / <em><?php echo e($__t('Note')); ?></em></th>
						<th><?php echo e($__t('Amount')); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php $__currentLoopData = $listItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $listItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
					<tr>
						<td>
							<?php if(!empty($listItem->product_id)): ?> <?php echo e(FindObjectInArrayByPropertyValue($products, 'id', $listItem->product_id)->name); ?><br><?php endif; ?><em><?php echo nl2br($listItem->note); ?></em>
						</td>
						<td>
							<?php echo e($listItem->amount); ?> <?php if(!empty($listItem->product_id)): ?><?php echo e($__n($listItem->amount, FindObjectInArrayByPropertyValue($quantityunits, 'id', FindObjectInArrayByPropertyValue($products, 'id', $listItem->product_id)->qu_id_purchase)->name, FindObjectInArrayByPropertyValue($quantityunits, 'id', FindObjectInArrayByPropertyValue($products, 'id', $listItem->product_id)->qu_id_purchase)->name_plural)); ?><?php endif; ?>
						</td>
					</tr>
					<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
				</tbody>
			</table>
		</div>
	</div>
	<div class="row w-75">
		<div class="col">
			<h5><?php echo e($__t('Notes')); ?></h5>
			<p id="description-for-print"></p>
		</div>
	</div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout.default', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /www/views/shoppinglist.blade.php ENDPATH**/ ?>