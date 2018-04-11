@extends('layout.default')

@section('content')
<div class="col-sm-3 col-sm-offset-3 col-md-4 col-md-offset-2 main">

	<h1 class="page-header"><?php echo $title; ?></h1>

	<script>Grocy.EditMode = '<?php echo $mode; ?>';</script>

	<?php if ($mode == 'edit') : ?>
		<script>Grocy.EditObjectId = <?php echo $location->id; ?>;</script>
	<?php endif; ?>

	<form id="location-form">

		<div class="form-group">
			<label for="name">Name</label>
			<input type="text" class="form-control" required id="name" name="name" value="<?php if ($mode == 'edit') echo $location->name; ?>">
			<div class="help-block with-errors"></div>
		</div>

		<div class="form-group">
			<label for="description">Description</label>
			<textarea class="form-control" rows="2" id="description" name="description"><?php if ($mode == 'edit') echo $location->description; ?></textarea>
		</div>

		<button id="save-location-button" type="submit" class="btn btn-default">Save</button>

	</form>

</div>
@stop
