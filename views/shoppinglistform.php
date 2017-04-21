<div class="col-sm-3 col-sm-offset-3 col-md-4 col-md-offset-2 main">

	<h1 class="page-header"><?php echo $title; ?></h1>

	<script>Grocy.EditMode = '<?php echo $mode; ?>';</script>

	<?php if ($mode == 'edit') : ?>
		<script>Grocy.EditObjectId = <?php echo $listItem->id; ?>;</script>
	<?php endif; ?>

	<form id="shoppinglist-form">

		<div class="form-group">
			<label for="product_id">Product&nbsp;&nbsp;<i class="fa fa-barcode"></i></label>
			<select class="form-control combobox" id="product_id" name="product_id" value="<?php if ($mode == 'edit') echo $listItem->product_id; ?>" required>
				<option value=""></option>
				<?php foreach ($products as $product) : ?>
					<option data-additional-searchdata="<?php echo $product->barcode; ?>" value="<?php echo $product->id; ?>"><?php echo $product->name; ?></option>
				<?php endforeach; ?>
			</select>
			<div id="product-error" class="help-block with-errors"></div>
		</div>

		<div class="form-group">
			<label for="amount">Amount</label>
			<input type="number" class="form-control" id="amount" name="amount" value="<?php if ($mode == 'edit') echo $listItem->amount; else echo '1'; ?>" min="1" required>
			<div class="help-block with-errors"></div>
		</div>

		<button id="save-shoppinglist-button" type="submit" class="btn btn-default">Save</button>

	</form>

</div>
