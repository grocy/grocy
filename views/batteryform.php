<div class="col-sm-3 col-sm-offset-3 col-md-4 col-md-offset-2 main">

	<h1 class="page-header"><?php echo $title; ?></h1>

	<script>Grocy.EditMode = '<?php echo $mode; ?>';</script>

	<?php if ($mode == 'edit') : ?>
		<script>Grocy.EditObjectId = <?php echo $battery->id; ?>;</script>
	<?php endif; ?>

	<form id="battery-form">

		<div class="form-group">
			<label for="name">Name</label>
			<input type="text" class="form-control" required id="name" name="name" value="<?php if ($mode == 'edit') echo $battery->name; ?>">
			<div class="help-block with-errors"></div>
		</div>

		<div class="form-group">
			<label for="description">Description</label>
			<input type="text" class="form-control" id="description" name="description" value="<?php if ($mode == 'edit') echo $battery->description; ?>">
		</div>

		<div class="form-group">
			<label for="name">Used in</label>
			<input type="text" class="form-control" id="used_in" name="used_in" value="<?php if ($mode == 'edit') echo $battery->used_in; ?>">
		</div>

		<button id="save-battery-button" type="submit" class="btn btn-default">Save</button>

	</form>

</div>
