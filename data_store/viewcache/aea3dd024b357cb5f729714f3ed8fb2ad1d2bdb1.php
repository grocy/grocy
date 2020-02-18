<?php $__env->startSection('title', $__t('Recipes')); ?>
<?php $__env->startSection('activeNav', 'recipes'); ?>
<?php $__env->startSection('viewJsName', 'recipes'); ?>

<?php $__env->startSection('content'); ?>
<script>
	Grocy.QuantityUnits = <?php echo json_encode($quantityUnits); ?>;
	Grocy.QuantityUnitConversionsResolved = <?php echo json_encode($quantityUnitConversionsResolved); ?>;
</script>

<div class="row">
	
	<div class="col-xs-12 col-md-6 pb-3">
		<h1>
			<?php echo $__env->yieldContent('title'); ?>
			<a class="btn btn-outline-dark" href="<?php echo e($U('/recipe/new')); ?>">
				<i class="fas fa-plus"></i> <?php echo e($__t('Add')); ?>

			</a>
		</h1>

		<div class="row">
			<div class="col-6">
				<label for="search"><?php echo e($__t('Search')); ?></label> <i class="fas fa-search"></i>
				<input type="text" class="form-control" id="search">
			</div>

			<div class="col-6">
				<label for="status-filter"><?php echo e($__t('Filter by status')); ?></label> <i class="fas fa-filter"></i>
				<select class="form-control" id="status-filter">
					<option class="bg-white" value="all"><?php echo e($__t('All')); ?></option>
					<option class="bg-success" value="enoughtinstock"><?php echo e($__t('Enough in stock')); ?></option>
					<option class="bg-warning" value="enoughinstockwithshoppinglist"><?php echo e($__t('Not enough in stock, but already on the shopping list')); ?></option>
					<option class="bg-danger" value="notenoughinstock"><?php echo e($__t('Not enough in stock')); ?></option>
				</select>
			</div>
		</div>

		<ul class="nav nav-tabs mt-3">
			<li class="nav-item">
				<a class="nav-link active" id="list-tab" data-toggle="tab" href="#list"><?php echo e($__t('List')); ?></a>
			</li>
			<li class="nav-item">
				<a class="nav-link" id="gallery-tab" data-toggle="tab" href="#gallery"><?php echo e($__t('Gallery')); ?></a>
			</li>
		</ul>

		<div class="tab-content">

			<div class="tab-pane show active" id="list">
				<table id="recipes-table" class="table table-striped dt-responsive">
					<thead>
						<tr>
							<th><?php echo e($__t('Name')); ?></th>
							<th><?php echo e($__t('Servings')); ?></th>
							<th><?php echo e($__t('Requirements fulfilled')); ?></th>
							<th class="d-none">Hidden status for sorting of "Requirements fulfilled" column</th>
							<th class="d-none">Hidden status for filtering by status</th>
							<th class="d-none">Hidden recipe ingredient product names</th>

							<?php echo $__env->make('components.userfields_thead', array(
								'userfields' => $userfields
							), \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

						</tr>
					</thead>
					<tbody class="d-none">
						<?php $__currentLoopData = $recipes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $recipe): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
						<tr id="recipe-row-<?php echo e($recipe->id); ?>" data-recipe-id="<?php echo e($recipe->id); ?>">
							<td>
								<?php echo e($recipe->name); ?>

							</td>
							<td>
								<?php echo e($recipe->desired_servings); ?>

							</td>
							<td>
								<?php if(FindObjectInArrayByPropertyValue($recipesResolved, 'recipe_id', $recipe->id)->need_fulfilled == 1): ?><i class="fas fa-check text-success"></i><?php elseif(FindObjectInArrayByPropertyValue($recipesResolved, 'recipe_id', $recipe->id)->need_fulfilled_with_shopping_list == 1): ?><i class="fas fa-exclamation text-warning"></i><?php else: ?><i class="fas fa-times text-danger"></i><?php endif; ?>
								<span class="timeago-contextual"><?php if(FindObjectInArrayByPropertyValue($recipesResolved, 'recipe_id', $recipe->id)->need_fulfilled == 1): ?><?php echo e($__t('Enough in stock')); ?><?php elseif(FindObjectInArrayByPropertyValue($recipesResolved, 'recipe_id', $recipe->id)->need_fulfilled_with_shopping_list == 1): ?><?php echo e($__n(FindObjectInArrayByPropertyValue($recipesResolved, 'recipe_id', $recipe->id)->missing_products_count, 'Not enough in stock, %s ingredient missing but already on the shopping list', 'Not enough in stock, %s ingredients missing but already on the shopping list')); ?><?php else: ?><?php echo e($__n(FindObjectInArrayByPropertyValue($recipesResolved, 'recipe_id', $recipe->id)->missing_products_count, 'Not enough in stock, %s ingredient missing', 'Not enough in stock, %s ingredients missing')); ?><?php endif; ?></span>
							</td>
							<td class="d-none">
								<?php echo e(FindObjectInArrayByPropertyValue($recipesResolved, 'recipe_id', $recipe->id)->missing_products_count); ?>

							</td>
							<td class="d-none">
								<?php if(FindObjectInArrayByPropertyValue($recipesResolved, 'recipe_id', $recipe->id)->need_fulfilled == 1): ?> enoughtinstock <?php elseif(FindObjectInArrayByPropertyValue($recipesResolved, 'recipe_id', $recipe->id)->need_fulfilled_with_shopping_list == 1): ?> enoughinstockwithshoppinglist <?php else: ?> notenoughinstock <?php endif; ?>
							</td>
							<td class="d-none">
								<?php $__currentLoopData = FindAllObjectsInArrayByPropertyValue($recipePositionsResolved, 'recipe_id', $recipe->id); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $recipePos): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
									<?php echo e(FindObjectInArrayByPropertyValue($products, 'id', $recipePos->product_id)->name . ' '); ?>

								<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
							</td>

							<?php echo $__env->make('components.userfields_tbody', array(
								'userfields' => $userfields,
								'userfieldValues' => FindAllObjectsInArrayByPropertyValue($userfieldValues, 'object_id', $recipe->id)
							), \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

						</tr>
						<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
					</tbody>
				</table>
			</div>

			<div class="tab-pane show" id="gallery">
				<div class="row no-gutters">
					<?php $__currentLoopData = $recipes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $recipe): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
					<div class="col-6 recipe-gallery-item-container">
						<a class="discrete-link recipe-gallery-item" data-recipe-id="<?php echo e($recipe->id); ?>" href="#">
							<div id="recipe-card-<?php echo e($recipe->id); ?>" class="card border-white mb-0 recipe-card">
								<?php if(!empty($recipe->picture_file_name)): ?>
								<img data-src="<?php echo e($U('/api/files/recipepictures/' . base64_encode($recipe->picture_file_name) . '?force_serve_as=picture&best_fit_width=400')); ?>" class="img-fluid lazy">
								<?php endif; ?>
								<div class="card-body text-center">
									<h5 class="card-title mb-1"><?php echo e($recipe->name); ?></h5>
									<p class="card-text">
										<?php if(FindObjectInArrayByPropertyValue($recipesResolved, 'recipe_id', $recipe->id)->need_fulfilled == 1): ?><i class="fas fa-check text-success"></i><?php elseif(FindObjectInArrayByPropertyValue($recipesResolved, 'recipe_id', $recipe->id)->need_fulfilled_with_shopping_list == 1): ?><i class="fas fa-exclamation text-warning"></i><?php else: ?><i class="fas fa-times text-danger"></i><?php endif; ?>
										<span class="timeago-contextual"><?php if(FindObjectInArrayByPropertyValue($recipesResolved, 'recipe_id', $recipe->id)->need_fulfilled == 1): ?><?php echo e($__t('Enough in stock')); ?><?php elseif(FindObjectInArrayByPropertyValue($recipesResolved, 'recipe_id', $recipe->id)->need_fulfilled_with_shopping_list == 1): ?><?php echo e($__n(FindObjectInArrayByPropertyValue($recipesResolved, 'recipe_id', $recipe->id)->missing_products_count, 'Not enough in stock, %s ingredient missing but already on the shopping list', 'Not enough in stock, %s ingredients missing but already on the shopping list')); ?><?php else: ?><?php echo e($__n(FindObjectInArrayByPropertyValue($recipesResolved, 'recipe_id', $recipe->id)->missing_products_count, 'Not enough in stock, %s ingredient missing', 'Not enough in stock, %s ingredients missing')); ?><?php endif; ?></span>
									</p>
								</div>
							</div>
						</a>
					</div>
				<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
				</div>
			</div>
			
		</div>
	</div>

	<?php if($selectedRecipe !== null): ?>
	<div class="col-xs-12 col-md-6">
		<div class="card-header">
			<i class="fas fa-cocktail"></i> <?php echo e($selectedRecipe->name); ?>&nbsp;&nbsp;
			<a id="selectedRecipeConsumeButton" class="btn btn-sm btn-outline-success py-0 hide-when-embedded hide-on-fullscreen-card <?php if(FindObjectInArrayByPropertyValue($recipesResolved, 'recipe_id', $selectedRecipe->id)->need_fulfilled == 0): ?> disabled <?php endif; ?>" href="#" data-toggle="tooltip" title="<?php echo e($__t('Consume all ingredients needed by this recipe')); ?>" data-recipe-id="<?php echo e($selectedRecipe->id); ?>" data-recipe-name="<?php echo e($selectedRecipe->name); ?>">
				<i class="fas fa-utensils"></i>
			</a>
			<a class="btn btn-sm btn-outline-primary py-0 recipe-order-missing-button hide-when-embedded hide-on-fullscreen-card <?php if(FindObjectInArrayByPropertyValue($recipesResolved, 'recipe_id', $selectedRecipe->id)->need_fulfilled_with_shopping_list == 1): ?> disabled <?php endif; ?>" href="#" data-toggle="tooltip" title="<?php echo e($__t('Put missing products on shopping list')); ?>" data-recipe-id="<?php echo e($selectedRecipe->id); ?>" data-recipe-name="<?php echo e($selectedRecipe->name); ?>">
				<i class="fas fa-cart-plus"></i>
			</a>&nbsp;&nbsp;
			<a id="selectedRecipeEditButton" class="btn btn-sm btn-outline-info hide-when-embedded hide-on-fullscreen-card py-0" href="<?php echo e($U('/recipe/')); ?><?php echo e($selectedRecipe->id); ?>">
				<i class="fas fa-edit"></i>
			</a>
			<a id="selectedRecipeDeleteButton" class="btn btn-sm btn-outline-danger hide-when-embedded hide-on-fullscreen-card py-0" href="#" data-recipe-id="<?php echo e($selectedRecipe->id); ?>" data-recipe-name="<?php echo e($selectedRecipe->name); ?>">
				<i class="fas fa-trash"></i>
			</a>
			<a id="selectedRecipeToggleFullscreenButton" class="btn btn-sm btn-outline-secondary py-0 hide-when-embedded float-right" href="#" data-toggle="tooltip" title="<?php echo e($__t('Expand to fullscreen')); ?>">
				<i class="fas fa-expand-arrows-alt"></i>
			</a>
		</div>
		<div id="selectedRecipeCard" class="card">
			<div class="card-body mb-0 pb-0">
				<div class="row">
					<div class="col-4">
						<?php echo $__env->make('components.numberpicker', array(
							'id' => 'servings-scale',
							'label' => 'Servings',
							'min' => 1,
							'value' => $selectedRecipe->desired_servings,
							'invalidFeedback' => $__t('This cannot be lower than %s', '1'),
							'additionalAttributes' => 'data-recipe-id="' . $selectedRecipe->id . '"'
						), \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
					</div>
					<?php if(!empty($selectedRecipeTotalCalories) && intval($selectedRecipeTotalCalories) > 0): ?>
					<div class="col-2">
						<label><?php echo e($__t('Energy (kcal)')); ?></label>
						<p class="mb-0">
							<h3 class="locale-number locale-number-generic pt-0"><?php echo e($selectedRecipeTotalCalories); ?></h3>
						</p>
					</div>
					<?php endif; ?>
					<?php if(GROCY_FEATURE_FLAG_STOCK_PRICE_TRACKING): ?>
					<div class="col-6">
						<label><?php echo e($__t('Costs')); ?>&nbsp;&nbsp;
							<span class="small text-muted"><?php echo e($__t('Based on the prices of the last purchase per product')); ?></span>
						</label>
						<p class="mb-0">
							<h3 class="locale-number locale-number-currency pt-0"><?php echo e($selectedRecipeTotalCosts); ?></h3>
						</p>
					</div>
					<?php endif; ?>
				</div>
			</div>

			<!-- Subrecipes first -->
			<?php $__currentLoopData = $selectedRecipeSubRecipes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $selectedRecipeSubRecipe): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
				<div class="card-body">
					<h3 class="mb-0"><?php echo e($selectedRecipeSubRecipe->name); ?></h3>
				</div>

				<?php if(!empty($selectedRecipeSubRecipe->picture_file_name)): ?>
					<p class="w-75 mx-auto txt-center"><img src="<?php echo e($U('/api/files/recipepictures/' . base64_encode($selectedRecipeSubRecipe->picture_file_name) . '?force_serve_as=picture&best_fit_width=400')); ?>" class="img-fluid img-thumbnail lazy"></p>
				<?php endif; ?>

				<?php $selectedRecipeSubRecipePositionsFiltered = FindAllObjectsInArrayByPropertyValue($selectedRecipeSubRecipesPositions, 'child_recipe_id', $selectedRecipeSubRecipe->id); ?>
				<?php if(count($selectedRecipeSubRecipePositionsFiltered) > 0): ?>
				<div class="card-body">
					<h5 class="mb-0"><?php echo e($__t('Ingredients')); ?></h5>
				</div>
				<ul class="list-group list-group-flush">
					<?php $lastGroup = 'undefined'; ?>
					<?php $__currentLoopData = $selectedRecipeSubRecipePositionsFiltered; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $selectedRecipePosition): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
					<?php if($lastGroup != $selectedRecipePosition->ingredient_group): ?>
						<h5 class="mb-2 mt-2 ml-4"><strong><?php echo e($selectedRecipePosition->ingredient_group); ?></strong></h5>
					<?php endif; ?>
					<li class="list-group-item">
						<?php
							$product = FindObjectInArrayByPropertyValue($products, 'id', $selectedRecipePosition->product_id);
							$productQuConversions = FindAllObjectsInArrayByPropertyValue($quantityUnitConversionsResolved, 'product_id', $product->id);
							$productQuConversions = FindAllObjectsInArrayByPropertyValue($productQuConversions, 'from_qu_id', $product->qu_id_stock);
							$productQuConversion = FindObjectInArrayByPropertyValue($productQuConversions, 'to_qu_id', $selectedRecipePosition->qu_id);
							if ($productQuConversion)
							{
								$selectedRecipePosition->recipe_amount = $selectedRecipePosition->recipe_amount * $productQuConversion->factor;
							}
						?>
						<?php if(!empty($selectedRecipePosition->recipe_variable_amount)): ?>
							<?php echo e($selectedRecipePosition->recipe_variable_amount); ?>

						<?php else: ?>
							<span class="llocale-number locale-number-quantity-amount"><?php if($selectedRecipePosition->recipe_amount == round($selectedRecipePosition->recipe_amount, 2)): ?><?php echo e(round($selectedRecipePosition->recipe_amount, 2)); ?><?php else: ?><?php echo e($selectedRecipePosition->recipe_amount); ?><?php endif; ?></span>
						<?php endif; ?>
						<?php echo e($__n($selectedRecipePosition->recipe_amount, FindObjectInArrayByPropertyValue($quantityUnits, 'id', $selectedRecipePosition->qu_id)->name, FindObjectInArrayByPropertyValue($quantityUnits, 'id', $selectedRecipePosition->qu_id)->name_plural)); ?> <?php echo e(FindObjectInArrayByPropertyValue($products, 'id', $selectedRecipePosition->product_id)->name); ?>

						<?php if($selectedRecipePosition->need_fulfilled == 1): ?><i class="fas fa-check text-success"></i><?php elseif($selectedRecipePosition->need_fulfilled_with_shopping_list == 1): ?><i class="fas fa-exclamation text-warning"></i><?php else: ?><i class="fas fa-times text-danger"></i><?php endif; ?>
						<span class="timeago-contextual"><?php if(FindObjectInArrayByPropertyValue($selectedRecipeSubRecipesPositions, 'recipe_pos_id', $selectedRecipePosition->id)->need_fulfilled == 1): ?> <?php echo e($__t('Enough in stock')); ?> <?php else: ?> <?php echo e($__t('Not enough in stock, %1$s missing, %2$s already on shopping list', round(FindObjectInArrayByPropertyValue($selectedRecipeSubRecipesPositions, 'recipe_pos_id', $selectedRecipePosition->id)->missing_amount, 2), round(FindObjectInArrayByPropertyValue($selectedRecipeSubRecipesPositions, 'recipe_pos_id', $selectedRecipePosition->id)->amount_on_shopping_list, 2))); ?> <?php endif; ?></span>

						<?php if(!empty($selectedRecipePosition->note)): ?>
						<div class="text-muted"><?php echo nl2br($selectedRecipePosition->note); ?></div>
						<?php endif; ?>
					</li>
					<?php $lastGroup = $selectedRecipePosition->ingredient_group; ?>
					<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
				</ul>
				<?php endif; ?>
				<?php if(!empty($selectedRecipeSubRecipe->description)): ?>
				<div class="card-body">
					<h5><?php echo e($__t('Preparation')); ?></h5>
					<?php echo $selectedRecipeSubRecipe->description; ?>

				</div>
				<?php endif; ?>
			<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

			<!-- Selected recipe -->
			<?php if(!empty($selectedRecipe->picture_file_name)): ?>
				<p class="w-75 mx-auto text-center"><img src="<?php echo e($U('/api/files/recipepictures/' . base64_encode($selectedRecipe->picture_file_name) . '?force_serve_as=picture&best_fit_width=400')); ?>" class="img-fluid img-thumbnail lazy"></p>
			<?php endif; ?>

			<?php if($selectedRecipePositionsResolved->count() > 0): ?>
			<div class="card-body">
				<h5 class="mb-0"><?php echo e($__t('Ingredients')); ?></h5>
			</div>
			<ul class="list-group list-group-flush">
				<?php $lastGroup = 'undefined'; ?>
				<?php $__currentLoopData = $selectedRecipePositionsResolved; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $selectedRecipePosition): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
				<?php if($lastGroup != $selectedRecipePosition->ingredient_group): ?>
					<h5 class="mb-2 mt-2 ml-4"><strong><?php echo e($selectedRecipePosition->ingredient_group); ?></strong></h5>
				<?php endif; ?>
				<li class="list-group-item">
					<?php
						$product = FindObjectInArrayByPropertyValue($products, 'id', $selectedRecipePosition->product_id);
						$productQuConversions = FindAllObjectsInArrayByPropertyValue($quantityUnitConversionsResolved, 'product_id', $product->id);
						$productQuConversions = FindAllObjectsInArrayByPropertyValue($productQuConversions, 'from_qu_id', $product->qu_id_stock);
						$productQuConversion = FindObjectInArrayByPropertyValue($productQuConversions, 'to_qu_id', $selectedRecipePosition->qu_id);
						if ($productQuConversion)
						{
							$selectedRecipePosition->recipe_amount = $selectedRecipePosition->recipe_amount * $productQuConversion->factor;
						}
					?>
					<?php if(!empty($selectedRecipePosition->recipe_variable_amount)): ?>
						<?php echo e($selectedRecipePosition->recipe_variable_amount); ?>

					<?php else: ?>
						<span class="locale-number locale-number-quantity-amount"><?php if($selectedRecipePosition->recipe_amount == round($selectedRecipePosition->recipe_amount, 2)): ?><?php echo e(round($selectedRecipePosition->recipe_amount, 2)); ?><?php else: ?><?php echo e($selectedRecipePosition->recipe_amount); ?><?php endif; ?></span>
					<?php endif; ?>
					<?php echo e($__n($selectedRecipePosition->recipe_amount, FindObjectInArrayByPropertyValue($quantityUnits, 'id', $selectedRecipePosition->qu_id)->name, FindObjectInArrayByPropertyValue($quantityUnits, 'id', $selectedRecipePosition->qu_id)->name_plural)); ?> <?php echo e(FindObjectInArrayByPropertyValue($products, 'id', $selectedRecipePosition->product_id)->name); ?>

					<?php if($selectedRecipePosition->need_fulfilled == 1): ?><i class="fas fa-check text-success"></i><?php elseif($selectedRecipePosition->need_fulfilled_with_shopping_list == 1): ?><i class="fas fa-exclamation text-warning"></i><?php else: ?><i class="fas fa-times text-danger"></i><?php endif; ?>
					<span class="timeago-contextual"><?php if(FindObjectInArrayByPropertyValue($recipePositionsResolved, 'recipe_pos_id', $selectedRecipePosition->id)->need_fulfilled == 1): ?> <?php echo e($__t('Enough in stock')); ?> <?php else: ?> <?php echo e($__t('Not enough in stock, %1$s missing, %2$s already on shopping list', round(FindObjectInArrayByPropertyValue($recipePositionsResolved, 'recipe_pos_id', $selectedRecipePosition->id)->missing_amount, 2), round(FindObjectInArrayByPropertyValue($recipePositionsResolved, 'recipe_pos_id', $selectedRecipePosition->id)->amount_on_shopping_list, 2))); ?> <?php endif; ?></span>

					<?php if(!empty($selectedRecipePosition->note)): ?>
					<div class="text-muted"><?php echo nl2br($selectedRecipePosition->note); ?></div>
					<?php endif; ?>
				</li>
				<?php $lastGroup = $selectedRecipePosition->ingredient_group; ?>
				<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
			</ul>
			<?php endif; ?>
			<?php if(!empty($selectedRecipe->description)): ?>
			<div class="card-body">
				<h5><?php echo e($__t('Preparation')); ?></h5>
				<?php echo $selectedRecipe->description; ?>

			</div>
			<?php endif; ?>
		</div>
	</div>
	<?php endif; ?>
</div>

<div id="missing-recipe-pos-list" class="list-group d-none mt-3">
	<?php $__currentLoopData = $recipePositionsResolved; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $recipePos): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
		<?php if(in_array($recipePos->recipe_id, $includedRecipeIdsAbsolute) && $recipePos->missing_amount > 0): ?>
			<a href="#" class="list-group-item list-group-item-action list-group-item-primary missing-recipe-pos-select-button">
				<div class="form-check form-check-inline">
					<input class="form-check-input missing-recipe-pos-product-checkbox" type="checkbox" data-product-id="<?php echo e($recipePos->product_id); ?>" checked>
				</div>
				<?php echo e(FindObjectInArrayByPropertyValue($products, 'id', $recipePos->product_id)->name); ?>

			</a>
		<?php endif; ?>
	<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout.default', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /www/views/recipes.blade.php ENDPATH**/ ?>