$('#save-inventory-button').on('click', function(e)
{
	e.preventDefault();

	var jsonForm = $('#inventory-form').serializeJSON();

	Grocy.Api.Get('stock/get-product-details/' + jsonForm.product_id,
		function (productDetails)
		{
			Grocy.Api.Get('stock/inventory-product/' + jsonForm.product_id + '/' + jsonForm.new_amount + '?bestbeforedate=' + Grocy.Components.DateTimePicker.GetValue(),
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
						Grocy.Components.DateTimePicker.SetValue('');
						Grocy.Components.ProductPicker.SetValue('');
						Grocy.Components.ProductPicker.GetInputElement().focus();
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

Grocy.Components.ProductPicker.GetPicker().on('change', function(e)
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

$('#new_amount').val('');
Grocy.FrontendHelpers.ValidateForm('inventory-form');

if (Grocy.Components.ProductPicker.InProductAddWorkflow() === false)
{
	Grocy.Components.ProductPicker.GetInputElement().focus();
}
else
{
	Grocy.Components.ProductPicker.GetPicker().trigger('change');
}

$('#new_amount').on('focus', function(e)
{
	if (Grocy.Components.ProductPicker.GetValue().length === 0)
	{
		Grocy.Components.ProductPicker.GetInputElement().focus();
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
		event.preventDefault();
		
		if (document.getElementById('inventory-form').checkValidity() === false) //There is at least one validation error
		{
			return false;
		}
		else
		{
			$('#save-inventory-button').click();
		}
	}
});

$('#new_amount').on('keypress', function(e)
{
	$('#new_amount').trigger('change');
});
	
Grocy.Components.DateTimePicker.GetInputElement().on('change', function(e)
{
	Grocy.FrontendHelpers.ValidateForm('inventory-form');
});

Grocy.Components.DateTimePicker.GetInputElement().on('keypress', function(e)
{
	Grocy.FrontendHelpers.ValidateForm('inventory-form');
});

$('#new_amount').on('keyup', function(e)
{
	var productId = Grocy.Components.ProductPicker.GetValue();
	var newAmount = parseInt($('#new_amount').val());
	
	if (productId)
	{
		Grocy.Api.Get('stock/get-product-details/' + productId,
			function(productDetails)
			{
				var productStockAmount = parseInt(productDetails.stock_amount || '0');
				
				if (newAmount > productStockAmount)
				{
					var amountToAdd = newAmount - productDetails.stock_amount;
					$('#inventory-change-info').text(L('This means #1 will be added to stock', amountToAdd.toString() + ' ' + productDetails.quantity_unit_stock.name));
					$('#inventory-change-info').removeClass('d-none');
					Grocy.Components.DateTimePicker.GetInputElement().attr('required', '');
				}
				else if (newAmount < productStockAmount)
				{
					var amountToRemove = productStockAmount - newAmount;
					$('#inventory-change-info').text(L('This means #1 will be removed from stock', amountToRemove.toString() + ' ' + productDetails.quantity_unit_stock.name));
					$('#inventory-change-info').removeClass('d-none');
					Grocy.Components.DateTimePicker.GetInputElement().removeAttr('required');
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
