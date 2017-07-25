<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">

	<h1 class="page-header">
		Habits
		<a class="btn btn-default" href="/habit/new" role="button">
			<i class="fa fa-plus"></i>&nbsp;Add
		</a>
	</h1>

	<div class="table-responsive">
		<table id="habits-table" class="table table-striped">
			<thead>
				<tr>
					<th>#</th>
					<th>Name</th>
					<th>Period type</th>
					<th>Period days</th>
					<th>Description</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($habits as $habit) : ?>
				<tr>
					<td class="fit-content">
						<a class="btn btn-info" href="/habit/<?php echo $habit->id; ?>" role="button">
							<i class="fa fa-pencil"></i>
						</a>
						<a class="btn btn-danger habit-delete-button" href="#" role="button" data-habit-id="<?php echo $habit->id; ?>" data-habit-name="<?php echo $habit->name; ?>">
							<i class="fa fa-trash"></i>
						</a>
					</td>
					<td>
						<?php echo $habit->name; ?>
					</td>
					<td>
						<?php echo $habit->period_type; ?>
					</td>
					<td>
						<?php echo $habit->period_days; ?>
					</td>
					<td>
						<?php echo $habit->description; ?>
					</td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>

</div>
