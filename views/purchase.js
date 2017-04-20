$('#save-purchase-button').on('click', function(e)
{
	e.preventDefault();

	var jsonForm = $('#purchase-form').serializeJSON();

	Grocy.FetchJson('/api/stock/get-product-details/' + jsonForm.product_id,
		function (productDetails)
		{
			var amount = jsonForm.amount * productDetails.product.qu_factor_purchase_to_stock;

			Grocy.FetchJson('/api/stock/add-product/' + jsonForm.product_id + '/' + amount + '?bestbeforedate=' + $('#best_before_date').val(),
				function(result)
				{
					var addBarcode = Grocy.GetUriParam('addbarcodetoselection');
					if (addBarcode !== undefined)
					{
						var existingBarcodes = productDetails.product.barcode || '';
						if (existingBarcodes.length === 0)
						{
							productDetails.product.barcode = addBarcode;
						}
						else
						{
							productDetails.product.barcode += ',' + addBarcode;
						}

						Grocy.PostJson('/api/edit-object/products/' + productDetails.product.id, productDetails.product,
							function (result) { },
							function(xhr)
							{
								console.error(xhr);
							}
						);
					}

					toastr.success('Added ' + amount + ' ' + productDetails.quantity_unit_stock.name + ' of ' + productDetails.product.name + ' to stock');

					if (addBarcode !== undefined)
					{
						window.location.href = '/purchase';
					}
					else
					{
						$('#amount').val(1);
						$('#best_before_date').val('');
						$('#product_id').val('');
						$('#product_id_text_input').focus();
						$('#product_id_text_input').val('');
						$('#product_id_text_input').trigger('change');
						$('#purchase-form').validator('validate');
					}
				},
				function(xhr)
				{
					console.error(xhr);
				}
			);
		},
		function(xhr)
		{
			console.error(xhr);
		}
	);
});

$('#product_id').on('change', function(e)
{
	var productId = $(e.target).val();

	if (productId)
	{
		Grocy.FetchJson('/api/stock/get-product-details/' + productId,
			function(productDetails)
			{
				$('#selected-product-name').text(productDetails.product.name);
				$('#selected-product-stock-amount').text(productDetails.stock_amount || '0');
				$('#selected-product-stock-qu-name').text(productDetails.quantity_unit_stock.name);
				$('#selected-product-purchase-qu-name').text(productDetails.quantity_unit_purchase.name);
				$('#selected-product-last-purchased').text((productDetails.last_purchased || 'never').substring(0, 10));
				$('#selected-product-last-purchased-timeago').text($.timeago(productDetails.last_purchased || ''));
				$('#selected-product-last-used').text((productDetails.last_used || 'never').substring(0, 10));
				$('#selected-product-last-used-timeago').text($.timeago(productDetails.last_used || ''));
				$('#new_amount_qu_unit').text(productDetails.quantity_unit_stock.name);
				
				if (productDetails.product.default_best_before_days.toString() !== '0')
				{
					$('#best_before_date').val(moment().add(productDetails.product.default_best_before_days, 'days').format('YYYY-MM-DD'));
				}

				Grocy.EmptyElementWhenMatches('#selected-product-last-purchased-timeago', 'NaN years ago');
				Grocy.EmptyElementWhenMatches('#selected-product-last-used-timeago', 'NaN years ago');
			},
			function(xhr)
			{
				console.error(xhr);
			}
		);
	}
});

$(function()
{
	$('.datepicker').datepicker(
	{
		format: 'yyyy-mm-dd',
		startDate: '+0d',
		todayHighlight: true,
		autoclose: true,
		calendarWeeks: true,
		orientation: 'bottom auto',
		weekStart: 1,
		showOnFocus: false
	});
	$('.datepicker').trigger('change');

	$('.combobox').combobox({
		appendId: '_text_input'
	});

	$('#product_id_text_input').on('change', function(e)
	{
		var input = $('#product_id_text_input').val().toString();
		var possibleOptionElement = $("#product_id option[data-additional-searchdata*='" + input + "']").first();
		
		if (Grocy.GetUriParam('addbarcodetoselection') === undefined && possibleOptionElement.length > 0)
		{
			$('#product_id').val(possibleOptionElement.val());
			$('#product_id').data('combobox').refresh();
			$('#product_id').trigger('change');
		}
		else
		{
			var optionElement = $("#product_id option:contains('" + input + "')").first();
			if (input.length > 0 && optionElement.length === 0 && Grocy.GetUriParam('addbarcodetoselection') === undefined	)
			{
				bootbox.dialog({
					message: '<strong>' + input + '</strong> could not be resolved to a product, how do you want to proceed?',
					title: 'Create or assign product',
					onEscape: function() { },
					buttons: {
						cancel: {
							label: 'Cancel',
							className: 'btn-default',
							callback: function() { }
						},
						addnewproduct: {
							label: 'Add as new product',
							className: 'btn-success',
							callback: function()
							{
								window.location.href = '/product/new?prefillname=' + encodeURIComponent(input) + '&returnto=' + encodeURIComponent(window.location.pathname);
							}
						},
						addbarcode: {
							label: 'Add as barcode to existing product',
							className: 'btn-info',
							callback: function()
							{
								window.location.href = '/purchase?addbarcodetoselection=' + encodeURIComponent(input);
							}
						}
					}
				});
			}
		}
	});

	$('#amount').val(1);
	$('#best_before_date').val('');
	$('#product_id').val('');
	$('#product_id_text_input').focus();
	$('#product_id_text_input').val('');
	$('#product_id_text_input').trigger('change');

	$('#purchase-form').validator({
		custom: {
			'isodate': function($el)
			{
				if ($el.val().length !== 0 && !moment($el.val(), 'YYYY-MM-DD', true).isValid())
				{
					return 'Wrong date format, needs to be YYYY-MM-DD';
				}
			}
		}
	});
	$('#purchase-form').validator('validate');

	$('#best_before_date').on('focus', function(e)
	{
		if ($('#product_id_text_input').val().length === 0)
		{
			$('#product_id_text_input').focus();
		}
	});

	$('#purchase-form input').keydown(function(event)
	{
		if (event.keyCode === 13) //Enter
		{
			if ($('#purchase-form').validator('validate').has('.has-error').length !== 0) //There is at least one validation error
			{
				event.preventDefault();
				return false;
			}
		}
	});

	var prefillProduct = Grocy.GetUriParam('createdproduct');
	if (prefillProduct !== undefined)
	{
		var possibleOptionElement = $("#product_id option[data-additional-searchdata*='" + prefillProduct + "']").first();
		if (possibleOptionElement.length === 0)
		{
			possibleOptionElement = $("#product_id option:contains('" + prefillProduct + "')").first();
		}

		if (possibleOptionElement.length > 0)
		{
			$('#product_id').val(possibleOptionElement.val());
			$('#product_id').data('combobox').refresh();
			$('#product_id').trigger('change');
			$('#best_before_date').focus();
		}
	}

	var addBarcode = Grocy.GetUriParam('addbarcodetoselection');
	if (addBarcode !== undefined)
	{
		$('#addbarcodetoselection').text(addBarcode);
		$('#flow-info-addbarcodetoselection').removeClass('hide');
	}
});

$('#best_before_date-datepicker-button').on('click', function(e)
{
	$('.datepicker').datepicker('show');
});

$('#best_before_date').on('change', function(e)
{
	var value = $('#best_before_date').val();
	if (value.length === 8 && $.isNumeric(value))
	{
		value = value.replace(/(\d{4})(\d{2})(\d{2})/, '$1-$2-$3');
		$('#best_before_date').val(value);
		$('#purchase-form').validator('validate');
	}
});

$('#best_before_date').on('keypress', function(e)
{
	var element = $(e.target);
	var value = element.val();
	var dateObj = moment(element.val(), 'YYYY-MM-DD', true);

	$('.datepicker').datepicker('hide');

	if (value.length === 0)
	{
		element.val(moment().format('YYYY-MM-DD'));
	}
	else if (dateObj.isValid())
	{
		if (e.keyCode === 38) //Up
		{
			element.val(dateObj.add(-1, 'days').format('YYYY-MM-DD'));
		}
		else if (e.keyCode === 40) //Down
		{
			element.val(dateObj.add(1, 'days').format('YYYY-MM-DD'));
		}
		else if (e.keyCode === 37) //Left
		{
			element.val(dateObj.add(-1, 'weeks').format('YYYY-MM-DD'));
		}
		else if (e.keyCode === 39) //Right
		{
			element.val(dateObj.add(1, 'weeks').format('YYYY-MM-DD'));
		}
	}

	$('#purchase-form').validator('validate');
});
