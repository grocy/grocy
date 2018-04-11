@extends('layout.default')

@section('content')
<div class="col-sm-3 col-sm-offset-3 col-md-4 col-md-offset-2 main">

	<h1 class="page-header"><?php echo $title; ?></h1>

	<script>Grocy.EditMode = '<?php echo $mode; ?>';</script>

	<?php if ($mode == 'edit') : ?>
		<script>Grocy.EditObjectId = <?php echo $habit->id; ?>;</script>
	<?php endif; ?>

	<form id="habit-form">

		<div class="form-group">
			<label for="name">Name</label>
			<input type="text" class="form-control" required id="name" name="name" value="<?php if ($mode == 'edit') echo $habit->name; ?>">
			<div class="help-block with-errors"></div>
		</div>

		<div class="form-group">
			<label for="description">Description</label>
			<textarea class="form-control" rows="2" id="description" name="description"><?php if ($mode == 'edit') echo $habit->description; ?></textarea>
		</div>

		<div class="form-group">
			<label for="period_type">Period type</label>
			<select required class="form-control input-group-habit-period-type" id="period_type" name="period_type">
				<?php foreach ($periodTypes as $periodType) : ?>
					<option <?php if ($mode == 'edit' && $periodType == $habit->period_type) echo 'selected="selected"'; ?> value="<?php echo $periodType; ?>"><?php echo $periodType; ?></option>
				<?php endforeach; ?>
			</select>
			<div class="help-block with-errors"></div>
		</div>

		<div class="form-group">
			<label for="period_days">Period days</label>
			<input type="number" class="form-control input-group-habit-period-type" id="period_days" name="period_days" value="<?php if ($mode == 'edit') echo $habit->period_days; ?>">
			<div class="help-block with-errors"></div>
		</div>

		<p id="habit-period-type-info" class="help-block text-muted"></p>

		<button id="save-habit-button" type="submit" class="btn btn-default">Save</button>

	</form>

</div>
@stop
