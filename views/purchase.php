<div class="col-sm-4 col-sm-offset-3 col-md-3 col-md-offset-2 main">

	<h1 class="page-header">Purchase</h1>

	<form id="purchase-form">

		<div class="form-group">
			<label for="product_id">Product&nbsp;&nbsp;<i class="fa fa-barcode"></i><span id="barcode-lookup-disabled-hint" class="small text-muted hide">&nbsp;&nbsp;Barcode lookup is disabled</span></label>
			<select class="form-control combobox" id="product_id" name="product_id" required>
				<option value=""></option>
				<?php foreach ($products as $product) : ?>
					<option data-additional-searchdata="<?php echo $product->barcode; ?>" value="<?php echo $product->id; ?>"><?php echo $product->name; ?></option>
				<?php endforeach; ?>
			</select>
			<div class="help-block with-errors"></div>
			<div id="flow-info-addbarcodetoselection" class="text-muted small hide"><strong><span id="addbarcodetoselection"></span></strong> will be added to the list of barcodes for the selected product on submit.</div>
		</div>

		<div class="form-group">
			<label for="best_before_date">Best before</label>
			<div class="input-group date">
				<input type="text" data-isodate="isodate" class="form-control datepicker" id="best_before_date" name="best_before_date" required autocomplete="off">
				<div id="best_before_date-datepicker-button" class="input-group-addon">
					<i class="fa fa-calendar"></i>
				</div>
			</div>
			<div class="help-block with-errors"></div>
		</div>

		<div class="form-group">
			<label for="amount">Amount&nbsp;&nbsp;<span id="amount_qu_unit" class="small text-muted"></span></label>
			<input type="number" class="form-control" id="amount" name="amount" value="1" min="1" required>
			<div class="help-block with-errors"></div>
		</div>

		<button id="save-purchase-button" type="submit" class="btn btn-default">OK</button>

	</form>

</div>

<div class="col-sm-6 col-md-5 col-lg-3 main well">
	<h3>Product overview <strong><span id="selected-product-name"></span></strong></h3>
	<h4><strong>Purchase quantity:</strong> <span id="selected-product-purchase-qu-name"></span></h4>

	<p>
		<strong>Stock amount:</strong> <span id="selected-product-stock-amount"></span> <span id="selected-product-stock-qu-name"></span><br>
		<strong>Last purchased:</strong> <span id="selected-product-last-purchased"></span> <time id="selected-product-last-purchased-timeago" class="timeago timeago-contextual"></time><br>
		<strong>Last used:</strong> <span id="selected-product-last-used"></span> <time id="selected-product-last-used-timeago" class="timeago timeago-contextual"></time>
	</p>
</div>
