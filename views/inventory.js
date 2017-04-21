$('#save-inventory-button').on('click', function(e)
{
	e.preventDefault();

	var jsonForm = $('#inventory-form').serializeJSON();

	Grocy.FetchJson('/api/stock/get-product-details/' + jsonForm.product_id,
		function (productDetails)
		{
			Grocy.FetchJson('/api/stock/inventory-product/' + jsonForm.product_id + '/' + jsonForm.new_amount + '?bestbeforedate=' + $('#best_before_date').val(),
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

					toastr.success('Stock amount of ' + productDetails.product.name  + ' is now ' + jsonForm.new_amount.toString() + ' ' + productDetails.quantity_unit_stock.name);
					Grocy.Wait(1000);

					if (addBarcode !== undefined)
					{
						window.location.href = '/inventory';
					}
					else
					{
						$('#inventory-change-info').hide();
						$('#new_amount').val('');
						$('#best_before_date').val('');
						$('#product_id').val('');
						$('#product_id_text_input').focus();
						$('#product_id_text_input').val('');
						$('#product_id_text_input').trigger('change');
						$('#inventory-form').validator('validate');
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
				$('#new_amount').attr('not-equal', productDetails.stock_amount);
				$('#new_amount_qu_unit').text(productDetails.quantity_unit_stock.name);

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
							label: 'Add as new <u><strong>p</strong></u>roduct',
							className: 'btn-success add-new-product-dialog-button',
							callback: function()
							{
								window.location.href = '/product/new?prefillname=' + encodeURIComponent(input) + '&returnto=' + encodeURIComponent(window.location.pathname);
							}
						},
						addbarcode: {
							label: 'Add as <u><strong>b</strong></u>arcode to existing product',
							className: 'btn-info add-new-barcode-dialog-button',
							callback: function()
							{
								window.location.href = '/inventory?addbarcodetoselection=' + encodeURIComponent(input);
							}
						}
					}
				}).on('keypress', function(e)
				{
					if (e.key === 'B' || e.key === 'b')
					{
						$('.add-new-barcode-dialog-button').click();
					}
					if (e.key === 'p' || e.key === 'P')
					{
						$('.add-new-product-dialog-button').click();
					}
				});
			}
		}
	});

	$('#new_amount').val('');
	$('#best_before_date').val('');
	$('#product_id').val('');
	$('#product_id_text_input').focus();
	$('#product_id_text_input').val('');
	$('#product_id_text_input').trigger('change');

	$('#inventory-form').validator({
		custom: {
			'isodate': function($el)
			{
				if ($el.val().length !== 0 && !moment($el.val(), 'YYYY-MM-DD', true).isValid())
				{
					return 'Wrong date format, needs to be YYYY-MM-DD';
				}
			},
			'notequal': function($el)
			{
				if ($el.val().length !== 0 && $el.val().toString() === $el.attr('not-equal').toString())
				{
					return 'This value cannot be equal to ' + $el.attr('not-equal').toString();
				}
			}
		}
	});
	$('#inventory-form').validator('validate');

	$('#new_amount').on('focus', function(e)
	{
		if ($('#product_id_text_input').val().length === 0)
		{
			$('#product_id_text_input').focus();
		}
	});

	$('#inventory-form input').keydown(function(event)
	{
		if (event.keyCode === 13) //Enter
		{
			if ($('#inventory-form').validator('validate').has('.has-error').length !== 0) //There is at least one validation error
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
			$('#new_amount').focus();
		}
	}

	var addBarcode = Grocy.GetUriParam('addbarcodetoselection');
	if (addBarcode !== undefined)
	{
		$('#addbarcodetoselection').text(addBarcode);
		$('#flow-info-addbarcodetoselection').removeClass('hide');
		$('#barcode-lookup-disabled-hint').removeClass('hide');
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
		$('#inventory-form').validator('validate');
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

	$('#inventory-form').validator('validate');
});

$('#new_amount').on('change', function(e)
{
	if ($('#product_id').parent().hasClass('has-error'))
	{
		$('#inventory-change-info').hide();
		return;
	}

	var productId = $('#product_id').val();
	var newAmount = $('#new_amount').val();

	if (productId)
	{
		Grocy.FetchJson('/api/stock/get-product-details/' + productId,
			function(productDetails)
			{
				var productStockAmount = productDetails.stock_amount || '0';

				if (newAmount > productStockAmount)
				{
					var amountToAdd = newAmount - productDetails.stock_amount;
					$('#inventory-change-info').text('This means ' + amountToAdd.toString() + ' ' + productDetails.quantity_unit_stock.name + ' will be added to stock');
					$('#inventory-change-info').show();
					$('#best_before_date').attr('required', 'required');
				}
				else if (newAmount < productStockAmount)
				{
					var amountToRemove = productStockAmount - newAmount;
					$('#inventory-change-info').text('This means ' + amountToRemove.toString() + ' ' + productDetails.quantity_unit_stock.name + ' will be removed from stock');
					$('#inventory-change-info').show();
					$('#best_before_date').removeAttr('required');
				}
				else
				{
					$('#inventory-change-info').hide();
				}

				$('#inventory-form').validator('update');
				$('#inventory-form').validator('validate');
			},
			function(xhr)
			{
				console.error(xhr);
			}
		);
	}
});
