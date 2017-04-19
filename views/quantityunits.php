<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">

	<h1 class="page-header">
		Quantity units
		<a class="btn btn-default" href="/quantityunit/new" role="button">
			<i class="fa fa-plus"></i>&nbsp;Add
		</a>
	</h1>

	<div class="table-responsive">
		<table id="quantityunits-table" class="table table-striped">
			<thead>
				<tr>
					<th>#</th>
					<th>Name</th>
					<th>Description</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($quantityunits as $quantityunit) : ?>
				<tr>
					<td class="fit-content">
						<a class="btn btn-info" href="/quantityunit/<?php echo $quantityunit->id; ?>" role="button">
							<i class="fa fa-pencil"></i>
						</a>
						<a class="btn btn-danger quantityunit-delete-button" href="#" role="button" data-quantityunit-id="<?php echo $quantityunit->id; ?>" data-quantityunit-name="<?php echo $quantityunit->name; ?>">
							<i class="fa fa-trash"></i>
						</a>
					</td>
					<td>
						<?php echo $quantityunit->name; ?>
					</td>
					<td>
						<?php echo $quantityunit->description; ?>
					</td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>

</div>
