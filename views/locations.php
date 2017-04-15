<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
	<h1 class="page-header">
		Locations
		<a class="btn btn-default" href="/location/new" role="button">
			<i class="fa fa-plus"></i>&nbsp;Add
		</a>
	</h1>

	<div class="table-responsive">
		<table class="table table-striped">
			<thead>
				<tr>
					<th>#</th>
					<th>Name</th>
					<th>Description</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($locations as $location) : ?>
				<tr>
					<td class="fit-content">
						<a class="btn btn-info" href="/location/<?php echo $location->id; ?>" role="button">
							<i class="fa fa-pencil"></i>
						</a>
						<a class="btn btn-danger location-delete-button" href="#" role="button" data-location-id="<?php echo $location->id; ?>" data-location-name="<?php echo $location->name; ?>">
							<i class="fa fa-trash"></i>
						</a>
					</td>
					<td>
						<?php echo $location->name; ?>
					</td>
					<td>
						<?php echo $location->description; ?>
					</td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
</div>
