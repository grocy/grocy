<?php if($mode == 'edit'): ?>
	<?php $__env->startSection('title', $__t('Edit recipe')); ?>
<?php else: ?>
	<?php $__env->startSection('title', $__t('Create recipe')); ?>
<?php endif; ?>

<?php $__env->startSection('viewJsName', 'recipeform'); ?>

<?php $__env->startPush('pageScripts'); ?>
	<script src="<?php echo e($U('/node_modules/datatables.net-rowgroup/js/dataTables.rowGroup.min.js?v=', true)); ?><?php echo e($version); ?>"></script>
	<script src="<?php echo e($U('/node_modules/datatables.net-rowgroup-bs4/js/rowGroup.bootstrap4.min.js?v=', true)); ?><?php echo e($version); ?>"></script>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('pageStyles'); ?>
	<link href="<?php echo e($U('/node_modules/datatables.net-rowgroup-bs4/css/rowGroup.bootstrap4.min.css?v=', true)); ?><?php echo e($version); ?>" rel="stylesheet">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="row">
	<div class="col">
		<h1><?php echo $__env->yieldContent('title'); ?></h1>

		<script>
			Grocy.EditMode = '<?php echo e($mode); ?>';
			Grocy.QuantityUnits = <?php echo json_encode($quantityunits); ?>;
			Grocy.QuantityUnitConversionsResolved = <?php echo json_encode($quantityUnitConversionsResolved); ?>;
		</script>

		<?php if($mode == 'edit'): ?>
			<script>Grocy.EditObjectId = <?php echo e($recipe->id); ?>;</script>

			<?php if(!empty($recipe->picture_file_name)): ?>
				<script>Grocy.RecipePictureFileName = '<?php echo e($recipe->picture_file_name); ?>';</script>
			<?php endif; ?>
		<?php endif; ?>
	</div>
</div>

<div class="row">
	<div class="col-xs-12 col-md-7 pb-3">
		<form id="recipe-form" novalidate>

			<div class="form-group">
				<label for="name"><?php echo e($__t('Name')); ?></label>
				<input type="text" class="form-control" required id="name" name="name" value="<?php if($mode == 'edit'): ?><?php echo e($recipe->name); ?><?php endif; ?>">
				<div class="invalid-feedback"><?php echo e($__t('A name is required')); ?></div>
			</div>

			<div class="form-group">
				<label for="description"><?php echo e($__t('Preparation')); ?></label>
				<textarea id="description" class="form-control wysiwyg-editor" name="description"><?php if($mode == 'edit'): ?><?php echo e($recipe->description); ?><?php endif; ?></textarea>
			</div>

			<?php if($mode == 'edit') { $value = $recipe->base_servings; } else { $value = 1; } ?>
			<?php echo $__env->make('components.numberpicker', array(
				'id' => 'base_servings',
				'label' => 'Servings',
				'min' => 1,
				'value' => $value,
				'invalidFeedback' => $__t('This cannot be lower than %s', '1'),
				'hint' => $__t('The ingredients listed here result in this amount of servings')
			), \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

			<div class="form-group">
				<div class="form-check">
					<input type="hidden" name="not_check_shoppinglist" value="0">
					<input <?php if($mode == 'edit' && $recipe->not_check_shoppinglist == 1): ?> checked <?php endif; ?> class="form-check-input" type="checkbox" id="not_check_shoppinglist" name="not_check_shoppinglist" value="1">
					<label class="form-check-label" for="not_check_shoppinglist"><?php echo e($__t('Do not check against the shopping list when adding missing items to it')); ?>&nbsp;&nbsp;
						<span class="small text-muted"><?php echo e($__t('By default the amount to be added to the shopping list is "needed amount - stock amount - shopping list amount" - when this is enabled, it is only checked against the stock amount, not against what is already on the shopping list')); ?></span>
					</label>
				</div>
			</div>

			<div class="form-group">
				<label for="recipe-picture"><?php echo e($__t('Picture')); ?>

					<span class="text-muted small"><?php echo e($__t('If you don\'t select a file, the current picture will not be altered')); ?></span>
				</label>
				<div class="custom-file">
					<input type="file" class="custom-file-input" id="recipe-picture" accept="image/*">
					<label class="custom-file-label" for="recipe-picture"><?php echo e($__t('No file selected')); ?></label>
				</div>
			</div>

			<?php echo $__env->make('components.userfieldsform', array(
				'userfields' => $userfields,
				'entity' => 'recipes'
			), \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

			<button id="save-recipe-button" class="btn btn-success"><?php echo e($__t('Save')); ?></button>

		</form>
	</div>

	<div class="col-xs-12 col-md-5 pb-3">
		<div class="row">
			<div class="col">
				<h2>
					<?php echo e($__t('Ingredients list')); ?>

					<a id="recipe-pos-add-button" class="btn btn-outline-dark" href="#">
						<i class="fas fa-plus"></i> <?php echo e($__t('Add')); ?>

					</a>
				</h2>
				
				<table id="recipes-pos-table" class="table table-sm table-striped dt-responsive">
					<thead>
						<tr>
							<th class="border-right"></th>
							<th><?php echo e($__t('Product')); ?></th>
							<th><?php echo e($__t('Amount')); ?></th>
							<th class="fit-content"><?php echo e($__t('Note')); ?></th>
							<th class="d-none">Hiden ingredient group</th>
						</tr>
					</thead>
					<tbody class="d-none">
						<?php if($mode == "edit"): ?>
						<?php $__currentLoopData = $recipePositions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $recipePosition): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
						<tr>
							<td class="fit-content border-right">
								<a class="btn btn-sm btn-info recipe-pos-edit-button" href="#" data-recipe-pos-id="<?php echo e($recipePosition->id); ?>">
									<i class="fas fa-edit"></i>
								</a>
								<a class="btn btn-sm btn-danger recipe-pos-delete-button" href="#" data-recipe-pos-id="<?php echo e($recipePosition->id); ?>" data-recipe-pos-name="<?php echo e(FindObjectInArrayByPropertyValue($products, 'id', $recipePosition->product_id)->name); ?>">
									<i class="fas fa-trash"></i>
								</a>
							</td>
							<td>
								<?php echo e(FindObjectInArrayByPropertyValue($products, 'id', $recipePosition->product_id)->name); ?>

							</td>
							<td>
								<?php
									$product = FindObjectInArrayByPropertyValue($products, 'id', $recipePosition->product_id);
									$productQuConversions = FindAllObjectsInArrayByPropertyValue($quantityUnitConversionsResolved, 'product_id', $product->id);
									$productQuConversions = FindAllObjectsInArrayByPropertyValue($productQuConversions, 'from_qu_id', $product->qu_id_stock);
									$productQuConversion = FindObjectInArrayByPropertyValue($productQuConversions, 'to_qu_id', $recipePosition->qu_id);
									if ($productQuConversion)
									{
										$recipePosition->amount = $recipePosition->amount * $productQuConversion->factor;
									}
								?>
								<?php if(!empty($recipePosition->variable_amount)): ?>
									<?php echo e($recipePosition->variable_amount); ?>

								<?php else: ?>
									<span class="locale-number locale-number-quantity-amount"><?php if($recipePosition->amount == round($recipePosition->amount)): ?><?php echo e(round($recipePosition->amount)); ?><?php else: ?><?php echo e($recipePosition->amount); ?><?php endif; ?></span>
								<?php endif; ?>
								<?php echo e($__n($recipePosition->amount, FindObjectInArrayByPropertyValue($quantityunits, 'id', $recipePosition->qu_id)->name, FindObjectInArrayByPropertyValue($quantityunits, 'id', $recipePosition->qu_id)->name_plural)); ?>

							</td>
							<td class="fit-content">
								<a class="btn btn-sm btn-info recipe-pos-show-note-button <?php if(empty($recipePosition->note)): ?> disabled <?php endif; ?>" href="#" data-toggle="tooltip" data-placement="top" title="<?php echo e($__t('Show notes')); ?>" data-recipe-pos-note="<?php echo e($recipePosition->note); ?>">
									<i class="fas fa-eye"></i>
								</a>
							</td>
							<td>
								<?php echo e($recipePosition->ingredient_group); ?>

							</td>
						</tr>
						<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
						<?php endif; ?>
					</tbody>
				</table>
			</div>
		</div>

		<div class="row mt-5">
			<div class="col">
				<h2>
					<?php echo e($__t('Included recipes')); ?>

					<a id="recipe-include-add-button" class="btn btn-outline-dark" href="#">
						<i class="fas fa-plus"></i> <?php echo e($__t('Add')); ?>

					</a>
				</h2>
				<table id="recipes-includes-table" class="table table-sm table-striped dt-responsive">
					<thead>
						<tr>
							<th class="border-right"></th>
							<th><?php echo e($__t('Recipe')); ?></th>
							<th><?php echo e($__t('Servings')); ?></th>
						</tr>
					</thead>
					<tbody class="d-none">
						<?php if($mode == "edit"): ?>
						<?php $__currentLoopData = $recipeNestings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $recipeNesting): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
						<tr>
							<td class="fit-content border-right">
								<a class="btn btn-sm btn-info recipe-include-edit-button" href="#" data-recipe-include-id="<?php echo e($recipeNesting->id); ?>" data-recipe-included-recipe-id="<?php echo e($recipeNesting->includes_recipe_id); ?>" data-recipe-included-recipe-servings="<?php echo e($recipeNesting->servings); ?>">
									<i class="fas fa-edit"></i>
								</a>
								<a class="btn btn-sm btn-danger recipe-include-delete-button" href="#" data-recipe-include-id="<?php echo e($recipeNesting->id); ?>" data-recipe-include-name="<?php echo e(FindObjectInArrayByPropertyValue($recipes, 'id', $recipeNesting->includes_recipe_id)->name); ?>">
									<i class="fas fa-trash"></i>
								</a>
							</td>
							<td>
								<?php echo e(FindObjectInArrayByPropertyValue($recipes, 'id', $recipeNesting->includes_recipe_id)->name); ?>

							</td>
							<td>
								<?php echo e($recipeNesting->servings); ?>

							</td>
						</tr>
						<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
						<?php endif; ?>
					</tbody>
				</table>
			</div>
		</div>

		<div class="row mt-5">
			<div class="col">
				<label class="mt-2"><?php echo e($__t('Picture')); ?></label>
				<button id="delete-current-recipe-picture-button" class="btn btn-sm btn-danger <?php if(empty($recipe->picture_file_name)): ?> disabled <?php endif; ?>"><i class="fas fa-trash"></i> <?php echo e($__t('Delete')); ?></button>
				<?php if(!empty($recipe->picture_file_name)): ?>
					<p><img id="current-recipe-picture" data-src="<?php echo e($U('/api/files/recipepictures/' . base64_encode($recipe->picture_file_name) . '?force_serve_as=picture&best_fit_width=400')); ?>" class="img-fluid img-thumbnail mt-2 lazy"></p>
					<p id="delete-current-recipe-picture-on-save-hint" class="form-text text-muted font-italic d-none"><?php echo e($__t('The current picture will be deleted when you save the recipe')); ?></p>
				<?php else: ?>
					<p id="no-current-recipe-picture-hint" class="form-text text-muted font-italic"><?php echo e($__t('No picture available')); ?></p>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="recipe-include-editform-modal" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content text-center">
			<div class="modal-header">
				<h4 id="recipe-include-editform-title" class="modal-title w-100"></h4>
			</div>
			<div class="modal-body">
				<form id="recipe-include-form" novalidate>

					<?php echo $__env->make('components.recipepicker', array(
						'recipes' => $recipes,
						'isRequired' => true
					), \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

					<?php echo $__env->make('components.numberpicker', array(
						'id' => 'includes_servings',
						'label' => 'Servings',
						'min' => 1,
						'value' => '1',
						'invalidFeedback' => $__t('This cannot be lower than %s', '1')
					), \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo e($__t('Cancel')); ?></button>
				<button id="save-recipe-include-button" data-dismiss="modal" class="btn btn-success"><?php echo e($__t('Save')); ?></button>
			</div>
		</div>
	</div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout.default', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /www/views/recipeform.blade.php ENDPATH**/ ?>