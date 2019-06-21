$('#save-inventory-button').on('click', function(e)
{
	e.preventDefault();

	var jsonForm = $('#inventory-form').serializeJSON();
	Grocy.FrontendHelpers.BeginUiBusy("inventory-form");

	Grocy.Api.Get('stock/products/' + jsonForm.product_id,
		function(productDetails)
		{
			var price = "";
			if (!jsonForm.price.toString().isEmpty())
			{
				price = parseFloat(jsonForm.price).toFixed(2);
			}

			var jsonData = { };
			jsonData.new_amount = jsonForm.new_amount;
			jsonData.best_before_date = Grocy.Components.DateTimePicker.GetValue();
			jsonData.location_id = Grocy.Components.LocationPicker.GetValue();
			jsonData.price = price;

			Grocy.Api.Post('stock/products/' + jsonForm.product_id + '/inventory', jsonData,
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

						Grocy.Api.Put('objects/products/' + productDetails.product.id, productDetails.product,
							function(result)
							{
								$("#flow-info-addbarcodetoselection").addClass("d-none");
								$('#barcode-lookup-disabled-hint').addClass('d-none');
								window.history.replaceState({ }, document.title, U("/inventory"));
							},
							function(xhr)
							{
								console.error(xhr);
							}
						);
					}

					Grocy.FrontendHelpers.EndUiBusy("inventory-form");
					toastr.success(__t('Stock amount of %1$s is now %2$s', productDetails.product.name, jsonForm.new_amount + " " + __n(jsonForm.new_amount, productDetails.quantity_unit_stock.name, productDetails.quantity_unit_stock.name_plural)) + '<br><a class="btn btn-secondary btn-sm mt-2" href="#" onclick="UndoStockBooking(' + result.id + ')"><i class="fas fa-undo"></i> ' + __t("Undo") + '</a>');

					$('#inventory-change-info').addClass('d-none');
					$("#tare-weight-handling-info").addClass("d-none");
					$("#new_amount").attr("min", "0");
					$("#new_amount").attr("step", "1");
					$("#new_amount").parent().find(".invalid-feedback").text(__t('The amount cannot be lower than %s', '0'));
					$('#new_amount').val('');
					$('#new_amount_qu_unit').text("");
					$('#price').val('');
					Grocy.Components.DateTimePicker.Clear();
					Grocy.Components.ProductPicker.SetValue('');
					Grocy.Components.ProductPicker.GetInputElement().focus();
					Grocy.FrontendHelpers.ValidateForm('inventory-form');
				},
				function(xhr)
				{
					Grocy.FrontendHelpers.EndUiBusy("inventory-form");
					console.error(xhr);
				}
			);
		},
		function(xhr)
		{
			Grocy.FrontendHelpers.EndUiBusy("inventory-form");
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

		Grocy.Api.Get('stock/products/' + productId,
			function(productDetails)
			{
				$('#new_amount').attr('data-not-equal', productDetails.stock_amount);
				$('#new_amount_qu_unit').text(productDetails.quantity_unit_stock.name);

				if (productDetails.product.allow_partial_units_in_stock == 1)
				{
					$("#new_amount").attr("min", "0.01");
					$("#new_amount").attr("step", "0.01");
					$("#new_amount").parent().find(".invalid-feedback").text(__t('The amount cannot be lower than %1$s or equal %2$s', 0.01.toLocaleString(), productDetails.stock_amount.toLocaleString()));
				}
				else
				{
					$("#new_amount").attr("min", "0");
					$("#new_amount").attr("step", "1");
					$("#new_amount").parent().find(".invalid-feedback").text(__t('The amount cannot be lower than %1$s or equal %2$s', '0', productDetails.stock_amount.toLocaleString()));
				}

				if (productDetails.product.enable_tare_weight_handling == 1)
				{
					$("#new_amount").attr("min", productDetails.product.tare_weight);
					$("#new_amount").parent().find(".invalid-feedback").text(__t('The amount cannot be lower than %1$s or equal %2$s', parseFloat(productDetails.product.tare_weight).toLocaleString({ minimumFractionDigits: 0, maximumFractionDigits: 2 }), productDetails.stock_amount.toLocaleString()));
					$("#tare-weight-handling-info").removeClass("d-none");
				}
				else
				{
					$("#tare-weight-handling-info").addClass("d-none");
				}

				$('#price').val(productDetails.last_price);
				Grocy.Components.LocationPicker.SetId(productDetails.location.id);
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
		Grocy.Api.Get('stock/products/' + productId,
			function(productDetails)
			{
				var productStockAmount = parseFloat(productDetails.stock_amount || parseFloat('0'));
				
				var containerWeight = parseFloat("0");
				if (productDetails.product.enable_tare_weight_handling == 1)
				{
					containerWeight = parseFloat(productDetails.product.tare_weight);
				}

				var estimatedBookingAmount = Math.abs(newAmount - productStockAmount - containerWeight);
				$('#inventory-change-info').removeClass('d-none');

				if (productDetails.product.enable_tare_weight_handling == 1 && newAmount < containerWeight)
				{
					$('#inventory-change-info').addClass('d-none');
				}
				else if (newAmount > productStockAmount + containerWeight)
				{
					$('#inventory-change-info').text(__t('This means %s will be added to stock', estimatedBookingAmount.toLocaleString() + ' ' + __n(estimatedBookingAmount, productDetails.quantity_unit_stock.name, productDetails.quantity_unit_stock.name_plural)));
					Grocy.Components.DateTimePicker.GetInputElement().attr('required', '');
					Grocy.Components.LocationPicker.GetInputElement().attr('required', '');
				}
				else if (newAmount < productStockAmount + containerWeight)
				{
					$('#inventory-change-info').text(__t('This means %s will be removed from stock', estimatedBookingAmount.toLocaleString() + ' ' + __n(estimatedBookingAmount, productDetails.quantity_unit_stock.name, productDetails.quantity_unit_stock.name_plural)));
					Grocy.Components.DateTimePicker.GetInputElement().removeAttr('required');
					Grocy.Components.LocationPicker.GetInputElement().removeAttr('required');
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

function UndoStockBooking(bookingId)
{
	Grocy.Api.Post('stock/bookings/' + bookingId.toString() + '/undo', { },
		function(result)
		{
			toastr.success(__t("Booking successfully undone"));
		},
		function(xhr)
		{
			console.error(xhr);
		}
	);
};
