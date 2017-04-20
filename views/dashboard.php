<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">

	<h1 class="page-header">Dashboard</h1>

	<h3>Stock overview</h3>

	<div>
		<p class="btn btn-lg btn-warning no-real-button"><strong><?php echo count(GrocyPhpHelper::FindAllObjectsInArrayByPropertyValue($currentStock, 'best_before_date', date('Y-m-d', strtotime('+5 days')), '<')); ?></strong> products expiring within the next 5 days</p>
		<p class="btn btn-lg btn-danger no-real-button"><strong><?php echo count(GrocyPhpHelper::FindAllObjectsInArrayByPropertyValue($currentStock, 'best_before_date', date('Y-m-d', strtotime('-1 days')), '<')); ?></strong> products are already expired</p>
	</div>

	<div class="table-responsive">
		<table id="current-stock-table" class="table table-striped">
			<thead>
				<tr>
					<th>Product</th>
					<th>Amount</th>
					<th>Next best before date</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($currentStock as $currentStockEntry) : ?>
				<tr class="<?php if ($currentStockEntry->best_before_date < date('Y-m-d', strtotime('-1 days'))) echo 'error-bg'; else if ($currentStockEntry->best_before_date < date('Y-m-d', strtotime('+5 days'))) echo 'warning-bg'; ?>">
					<td>
						<?php echo GrocyPhpHelper::FindObjectInArrayByPropertyValue($products, 'id', $currentStockEntry->product_id)->name; ?>
					</td>
					<td>
						<?php echo $currentStockEntry->amount; ?>
					</td>
					<td>
						<?php echo $currentStockEntry->best_before_date; ?>
						<time class="timeago timeago-contextual" datetime="<?php echo $currentStockEntry->best_before_date; ?>"></time>
					</td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>

</div>
