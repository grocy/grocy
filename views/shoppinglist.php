<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">

	<h1 class="page-header">
		Shopping List
		<a class="btn btn-default" href="/shoppinglist/new" role="button">
			<i class="fa fa-plus"></i>&nbsp;Add
		</a>
		<a id="add-products-below-min-stock-amount" class="btn btn-info" href="#" role="button">
			<i class="fa fa-plus"></i>&nbsp;Add products that are below defined min. stock amount
		</a>
	</h1>

	<div class="table-responsive">
		<table id="shoppinglist-table" class="table table-striped">
			<thead>
				<tr>
					<th>#</th>
					<th>Product</th>
					<th>Amount</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($listItems as $listItem) : ?>
				<tr>
					<td class="fit-content">
						<a class="btn btn-info" href="/shoppinglist/<?php echo $listItem->id; ?>" role="button">
							<i class="fa fa-pencil"></i>
						</a>
						<a class="btn btn-danger shoppinglist-delete-button" href="#" role="button" data-shoppinglist-id="<?php echo $listItem->id; ?>">
							<i class="fa fa-trash"></i>
						</a>
					</td>
					<td>
						<?php echo GrocyPhpHelper::FindObjectInArrayByPropertyValue($products, 'id', $listItem->product_id)->name; ?>
					</td>
					<td>
						<?php echo $listItem->amount + $listItem->amount_autoadded . ' ' . GrocyPhpHelper::FindObjectInArrayByPropertyValue($quantityunits, 'id', GrocyPhpHelper::FindObjectInArrayByPropertyValue($products, 'id', $listItem->product_id)->qu_id_purchase)->name; ?>
					</td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>

</div>
