<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
	<h1 class="page-header">Dashboard</h1>

	<h3>Current stock</h3>
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
				<tr>
					<td>
						<?php echo GrocyPhpHelper::FindObjectInArrayByPropertyValue($products, 'id', $currentStockEntry->product_id)->name; ?>
					</td>
					<td>
						<?php echo $currentStockEntry->amount; ?>
					</td>
					<td>
						<?php echo $currentStockEntry->best_before_date; ?> <time class="timeago timeago-contextual" datetime="<?php echo $currentStockEntry->best_before_date; ?>"></time>
					</td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
</div>
