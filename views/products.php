<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
	<h1 class="page-header">
		Products
		<a class="btn btn-default" href="/product/new" role="button">
			<i class="fa fa-plus"></i>&nbsp;Add
		</a>
	</h1>

	<div class="table-responsive">
		<table class="table table-striped">
			<thead>
				<tr>
					<th>#</th>
					<th>Name</th>
					<th>Location</th>
					<th>QU purchase</th>
					<th>QU stock</th>
					<th>QU factor</th>
					<th>Description</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($products as $product) : ?>
				<tr>
					<td class="fit-content">
						<a class="btn btn-info" href="/product/<?php echo $product->id; ?>" role="button">
							<i class="fa fa-pencil"></i>
						</a>
						<a class="btn btn-danger product-delete-button" href="#" role="button" data-product-id="<?php echo $product->id; ?>" data-product-name="<?php echo $product->name; ?>">
							<i class="fa fa-trash"></i>
						</a>
					</td>
					<td>
						<?php echo $product->name; ?>
					</td>
					<td>
						<?php echo Grocy::FindObjectInArrayByPropertyValue($locations, 'id', $product->location_id)->name; ?>
					</td>
					<td>
						<?php echo Grocy::FindObjectInArrayByPropertyValue($quantityunits, 'id', $product->qu_id_purchase)->name; ?>
					</td>
					<td>
						<?php echo Grocy::FindObjectInArrayByPropertyValue($quantityunits, 'id', $product->qu_id_stock)->name; ?>
					</td>
					<td>
						<?php echo $product->qu_factor_purchase_to_stock; ?>
					</td>
					<td>
						<?php echo $product->description; ?>
					</td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
</div>
