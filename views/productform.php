<div class="col-sm-3 col-sm-offset-3 col-md-4 col-md-offset-2 main">
	<h1 class="page-header"><?php echo $title; ?></h1>

	<script>Grocy.EditMode = '<?php echo $mode; ?>';</script>

	<?php if ($mode == 'edit') : ?>
		<script>Grocy.EditObjectId = <?php echo $product->id; ?>;</script>
	<?php endif; ?>

	<form id="product-form">
		<div class="form-group">
			<label for="name">Name</label>
			<input type="text" class="form-control" required id="name" name="name" value="<?php if ($mode == 'edit') echo $product->name; ?>">
			<div class="help-block with-errors"></div>
		</div>
		<div class="form-group">
			<label for="description">Description</label>
			<textarea class="form-control" rows="2" id="description" name="description"><?php if ($mode == 'edit') echo $product->description; ?></textarea>
		</div>
		<div class="form-group tm-group">
			<label for="barcode-taginput">Barcode(s)&nbsp;&nbsp;<i class="fa fa-barcode"></i></label>
			<input type="text" class="form-control tm-input" id="barcode-taginput">
			<div id="barcode-taginput-container"></div>
		</div>
		<div class="form-group">
			<label for="location_id">Location</label>
			<select required class="form-control" id="location_id" name="location_id">
				<?php foreach ($locations as $location) : ?>
					<option <?php if ($mode == 'edit' && $location->id == $product->location_id) echo 'selected="selected"'; ?> value="<?php echo $location->id; ?>"><?php echo $location->name; ?></option>
				<?php endforeach; ?>
			</select>
			<div class="help-block with-errors"></div>
		</div>
		<div class="form-group">
			<label for="qu_id_purchase">Quantity unit purchase</label>
			<select required class="form-control input-group-qu" id="qu_id_purchase" name="qu_id_purchase">
				<?php foreach ($quantityunits as $quantityunit) : ?>
					<option <?php if ($mode == 'edit' && $quantityunit->id == $product->qu_id_purchase) echo 'selected="selected"'; ?> value="<?php echo $quantityunit->id; ?>"><?php echo $quantityunit->name; ?></option>
				<?php endforeach; ?>
			</select>
			<div class="help-block with-errors"></div>
		</div>
		<div class="form-group">
			<label for="qu_id_stock">Quantity unit stock</label>
			<select required class="form-control input-group-qu" id="qu_id_stock" name="qu_id_stock">
				<?php foreach ($quantityunits as $quantityunit) : ?>
					<option <?php if ($mode == 'edit' && $quantityunit->id == $product->qu_id_stock) echo 'selected="selected"'; ?> value="<?php echo $quantityunit->id; ?>"><?php echo $quantityunit->name; ?></option>
				<?php endforeach; ?>
			</select>
			<div class="help-block with-errors"></div>
		</div>
		<div class="form-group">
			<label for="qu_factor_purchase_to_stock">Factor purchase to stock quantity unit</label>
			<input required min="1" type="number" class="form-control input-group-qu" id="qu_factor_purchase_to_stock" name="qu_factor_purchase_to_stock" value="<?php if ($mode == 'edit') echo $product->qu_factor_purchase_to_stock; else echo '1'; ?>">
			<div class="help-block with-errors"></div>
		</div>
		<p id="qu-conversion-info" class="help-block text-muted"></p>
		<button id="save-product-button" type="submit" class="btn btn-default">Save</button>
	</form>
</div>
