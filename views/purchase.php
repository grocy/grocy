<div class="col-sm-3 col-sm-offset-3 col-md-3 col-md-offset-2 main">
	<h1 class="page-header">Record purchase</h1>

	<form id="purchase-form">
		<div class="form-group">
			<label for="product_id">Product</label>
			<select class="form-control combobox" id="product_id" name="product_id" required>
				<option value=""></option>
				<?php foreach ($products as $product) : ?>
					<option value="<?php echo $product->id; ?>"><?php echo $product->name; ?></option>
				<?php endforeach; ?>
			</select>
			<div class="input-group date">
				<input type="text" class="form-control" id="barcode" name="barcode" />
				<div class="input-group-addon">
					<i class="fa fa-barcode"></i>
				</div>
			</div>
			<div class="help-block with-errors"></div>
		</div>
		<div class="form-group">
			<label for="amount">Amount</label>
			<input type="number" class="form-control" id="amount" name="amount" value="1" min="1" required>
			<div class="help-block with-errors"></div>
		</div>
		<div class="form-group">
			<label for="best_before_date">Best before</label>
			<div class="input-group date">
				<input type="text" class="form-control datepicker" id="best_before_date" name="best_before_date" required>
				<div class="input-group-addon">
					<i class="fa fa-calendar"></i>
				</div>
			</div>
			<div class="help-block with-errors"></div>
		</div>
		<button id="save-purchase-button" type="submit" class="btn btn-default">OK</button>
	</form>
</div>

<div class="col-sm-3 col-md-3 main well">
	<h3>Product overview <strong><span id="selected-product-name"></span></strong></h3>
	<h4><strong>Purchase quantity:</strong> <span id="selected-product-purchase-qu-name"></span></h4>

	<p>
		<strong>Stock amount:</strong> <span id="selected-product-stock-amount"></span> <span id="selected-product-stock-qu-name"></span><br />
		<strong>Last purchased:</strong> <span id="selected-product-last-purchased"></span><br />
		<strong>Last used:</strong> <span id="selected-product-last-used"></span>
	</p>
</div>