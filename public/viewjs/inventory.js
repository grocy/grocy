$('#save-inventory-button').on('click', function(e)
{
	e.preventDefault();

	var jsonForm = $('#inventory-form').serializeJSON();

	Grocy.Api.Get('stock/get-product-details/' + jsonForm.product_id,
		function (productDetails)
		{
			Grocy.Api.Get('stock/inventory-product/' + jsonForm.product_id + '/' + jsonForm.new_amount + '?bestbeforedate=' + $('#best_before_date').val(),
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

						Grocy.Api.Get('edit-object/products/' + productDetails.product.id, productDetails.product,
							function (result) { },
							function(xhr)
							{
								console.error(xhr);
							}
						);
					}

					toastr.success(L('Stock amount of #1 is now #2 #3', productDetails.product.name, jsonForm.new_amount, productDetails.quantity_unit_stock.name));

					if (addBarcode !== undefined)
					{
						window.location.href = U('/inventory');
					}
					else
					{
						$('#inventory-change-info').addClass('d-none');
						$('#new_amount').val('');
						$('#best_before_date').val('');
						$('#product_id').val('');
						$('#product_id_text_input').focus();
						$('#product_id_text_input').val('');
						$('#product_id_text_input').trigger('change');
						Grocy.FrontendHelpers.ValidateForm('inventory-form');
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
				$('#new_amount').attr('not-equal', productDetails.stock_amount);
				$('#new_amount_qu_unit').text(productDetails.quantity_unit_stock.name);

				$('#new_amount').focus();
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
				message: L('#1 could not be resolved to a product, how do you want to proceed?', input),
				title: L('Create or assign product'),
				onEscape: function() { },
				size: 'large',
				backdrop: true,
				buttons: {
					cancel: {
						label: L('Cancel'),
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
							window.location.href = U('/inventory?addbarcodetoselection=' + encodeURIComponent(input));
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

$('#new_amount').val('');
$('#best_before_date').val('');
$('#product_id').val('');
$('#product_id_text_input').focus();
$('#product_id_text_input').val('');
$('#product_id_text_input').trigger('change');

$('#new_amount').on('focus', function(e)
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

$('#inventory-form input').keyup(function (event)
{
	Grocy.FrontendHelpers.ValidateForm('inventory-form');
});

$('#inventory-form input').keydown(function(event)
{
	if (event.keyCode === 13) //Enter
	{
		if (document.getElementById('inventory-form').checkValidity() === false) //There is at least one validation error
		{
			event.preventDefault();
			return false;
		}
		else
		{
			$('#save-inventory-button').click();
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
		$('#new_amount').focus();
	}
}

var addBarcode = GetUriParam('addbarcodetoselection');
if (addBarcode !== undefined)
{
	$('#addbarcodetoselection').text(addBarcode);
	$('#flow-info-addbarcodetoselection').removeClass('d-none');
	$('#barcode-lookup-disabled-hint').removeClass('d-none');
}

$('#new_amount').on('keypress', function(e)
{
	$('#new_amount').trigger('change');
});
	
$('#best_before_date').on('change', function(e)
{
	Grocy.FrontendHelpers.ValidateForm('inventory-form');
});

$('#best_before_date').on('keypress', function(e)
{
	Grocy.FrontendHelpers.ValidateForm('inventory-form');
});

$('#best_before_date').on('keydown', function(e)
{
	if (e.keyCode === 13) //Enter
	{
		$('#best_before_date').trigger('change');
	}
});	

$('#new_amount').on('keyup', function(e)
{
	if ($('#product_id').parent().hasClass('has-error'))
	{
		$('#inventory-change-info').addClass('d-none');
		return;
	}

	var productId = $('#product_id').val();
	var newAmount = $('#new_amount').val();

	if (productId)
	{
		Grocy.Api.Get('stock/get-product-details/' + productId,
			function(productDetails)
			{
				var productStockAmount = productDetails.stock_amount || '0';

				if (newAmount > productStockAmount)
				{
					var amountToAdd = newAmount - productDetails.stock_amount;
					$('#inventory-change-info').text(L('This means #1 will be added to stock', amountToAdd.toString() + ' ' + productDetails.quantity_unit_stock.name));
					$('#inventory-change-info').removeClass('d-none')
					$('#best_before_date').attr('required', 'required');
				}
				else if (newAmount < productStockAmount)
				{
					var amountToRemove = productStockAmount - newAmount;
					$('#inventory-change-info').text(L('This means #1 will be removed from stock', amountToRemove.toString() + ' ' + productDetails.quantity_unit_stock.name));
					$('#inventory-change-info').removeClass('d-none')
					$('#best_before_date').removeAttr('required');
				}
				else
				{
					$('#inventory-change-info').addClass('d-none');
				}

				Grocy.FrontendHelpers.ValidateForm('inventory-form');
			},
			function(xhr)
			{
				console.error(xhr);
			}
		);
	}
});
