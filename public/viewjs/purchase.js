$('#save-purchase-button').on('click', function(e)
{
	e.preventDefault();

	var jsonForm = $('#purchase-form').serializeJSON();

	Grocy.Api.Get('stock/get-product-details/' + jsonForm.product_id,
		function (productDetails)
		{
			var amount = jsonForm.amount * productDetails.product.qu_factor_purchase_to_stock;

			Grocy.Api.Get('stock/add-product/' + jsonForm.product_id + '/' + amount + '?bestbeforedate=' + $('#best_before_date').val(),
				function(result)
				{
					var addBarcode = GetUriParam('addbarcodetoselection');
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

						Grocy.Api.Post('edit-object/products/' + productDetails.product.id, productDetails.product,
							function (result) { },
							function(xhr)
							{
								console.error(xhr);
							}
						);
					}

					toastr.success(L('Added #1 #2 of #3 to stock', amount, productDetails.quantity_unit_stock.name, productDetails.product.name));

					if (addBarcode !== undefined)
					{
						window.location.href = U('/purchase');
					}
					else
					{
						$('#amount').val(0);
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
		Grocy.Components.ProductCard.Refresh(productId);

		Grocy.Api.Get('stock/get-product-details/' + productId,
			function(productDetails)
			{
				$('#amount_qu_unit').text(productDetails.quantity_unit_purchase.name);
				
				if (productDetails.product.default_best_before_days.toString() !== '0')
				{
					$('#best_before_date').val(moment().add(productDetails.product.default_best_before_days, 'days').format('YYYY-MM-DD'));
					$('#best_before_date').trigger('change');
					$('#amount').focus();
				}
				else
				{
					$('#best_before_date').focus();
				}
			},
			function(xhr)
			{
				console.error(xhr);
			}
		);
	}
});

$('.combobox').combobox({
	appendId: '_text_input'
});

$('#product_id_text_input').on('change', function(e)
{
	var input = $('#product_id_text_input').val().toString();
	var possibleOptionElement = $("#product_id option[data-additional-searchdata*='" + input + "']").first();
	
	if (GetUriParam('addbarcodetoselection') === undefined && possibleOptionElement.length > 0)
	{
		$('#product_id').val(possibleOptionElement.val());
		$('#product_id').data('combobox').refresh();
		$('#product_id').trigger('change');
	}
	else
	{
		var optionElement = $("#product_id option:contains('" + input + "')").first();
		if (input.length > 0 && optionElement.length === 0 && GetUriParam('addbarcodetoselection') === undefined	)
		{
			bootbox.dialog({
				message: L('"#1" could not be resolved to a product, how do you want to proceed?', input),
				title: L('Create or assign product'),
				onEscape: function() { },
				size: 'large',
				backdrop: true,
				buttons: {
					cancel: {
						label: 'Cancel',
						className: 'btn-default',
						callback: function() { }
					},
					addnewproduct: {
						label: '<strong>P</strong> ' + L('Add as new product'),
						className: 'btn-success add-new-product-dialog-button',
						callback: function()
						{
							window.location.href = U('/product/new?prefillname=' + encodeURIComponent(input) + '&returnto=' + encodeURIComponent(window.location.pathname));
						}
					},
					addbarcode: {
						label: '<strong>B</strong> ' + L('Add as barcode to existing product'),
						className: 'btn-info add-new-barcode-dialog-button',
						callback: function()
						{
							window.location.href = U('/purchase?addbarcodetoselection=' + encodeURIComponent(input));
						}
					},
					addnewproductwithbarcode: {
						label: '<strong>A</strong> ' + L('Add as new product and prefill barcode'),
						className: 'btn-warning add-new-product-with-barcode-dialog-button',
						callback: function()
						{
							window.location.href = U('/product/new?prefillbarcode=' + encodeURIComponent(input) + '&returnto=' + encodeURIComponent(window.location.pathname));
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
				if (e.key === 'a' || e.key === 'A')
				{
					$('.add-new-product-with-barcode-dialog-button').click();
				}
			});
		}
	}
});

$('#amount').val(0);
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
			else if (moment($el.val()).isValid())
			{
				if (moment($el.val()).isBefore(moment(), 'day'))
				{
					return 'This value cannot be before today.';
				}
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

$('#amount').on('focus', function(e)
{
	if ($('#product_id_text_input').val().length === 0)
	{
		$('#product_id_text_input').focus();
	}
	else
	{
		$(this).select();
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

var prefillProduct = GetUriParam('createdproduct');
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

var addBarcode = GetUriParam('addbarcodetoselection');
if (addBarcode !== undefined)
{
	$('#addbarcodetoselection').text(addBarcode);
	$('#flow-info-addbarcodetoselection').removeClass('hide');
	$('#barcode-lookup-disabled-hint').removeClass('hide');
}

$('#best_before_date').on('change', function(e)
{
	$('#purchase-form').validator('validate');
});

$('#best_before_date').on('keypress', function(e)
{
	$('#purchase-form').validator('validate');
});
