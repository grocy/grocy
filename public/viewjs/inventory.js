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
			if (Grocy.FeatureFlags.GROCY_FEATURE_FLAG_STOCK_PRICE_TRACKING) {
				jsonData.shopping_location_id = Grocy.Components.ShoppingLocationPicker.GetValue();
			}
			if (Grocy.FeatureFlags.GROCY_FEATURE_FLAG_STOCK_LOCATION_TRACKING)
			{
				jsonData.location_id = Grocy.Components.LocationPicker.GetValue();
			}
			
			jsonData.price = price;

			var bookingResponse = null;

			Grocy.Api.Post('stock/products/' + jsonForm.product_id + '/inventory', jsonData,
				function(result)
				{
					bookingResponse = result;
					
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

					Grocy.Api.Get('stock/products/' + jsonForm.product_id,
						function(result)
						{
							var successMessage = __t('Stock amount of %1$s is now %2$s', result.product.name, result.stock_amount + " " + __n(result.stock_amount, result.quantity_unit_stock.name, result.quantity_unit_stock.name_plural)) + '<br><a class="btn btn-secondary btn-sm mt-2" href="#" onclick="UndoStockTransaction(\'' + bookingResponse.transaction_id + '\')"><i class="fas fa-undo"></i> ' + __t("Undo") + '</a>';
							
							if (GetUriParam("embedded") !== undefined)
							{
								window.parent.postMessage(WindowMessageBag("ProductChanged", jsonForm.product_id), Grocy.BaseUrl);
								window.parent.postMessage(WindowMessageBag("ShowSuccessMessage", successMessage), Grocy.BaseUrl);
								window.parent.postMessage(WindowMessageBag("CloseAllModals"), Grocy.BaseUrl);
							}
							else
							{
								Grocy.FrontendHelpers.EndUiBusy("inventory-form");
								toastr.success(successMessage);

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
								if (Grocy.FeatureFlags.GROCY_FEATURE_FLAG_STOCK_PRICE_TRACKING) {
									Grocy.Components.ShoppingLocationPicker.SetValue('');
								}
								Grocy.Components.ProductPicker.GetInputElement().focus();
								Grocy.Components.ProductCard.Refresh(jsonForm.product_id);
								Grocy.FrontendHelpers.ValidateForm('inventory-form');
							}
						},
						function(xhr)
						{
							Grocy.FrontendHelpers.EndUiBusy();
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
					$("#new_amount").parent().find(".invalid-feedback").text(__t('The amount cannot be lower than %1$s or equal %2$s', parseFloat(productDetails.product.tare_weight).toLocaleString({ minimumFractionDigits: 0, maximumFractionDigits: 2 }), (parseFloat(productDetails.stock_amount) + parseFloat(productDetails.product.tare_weight)).toLocaleString()));
					$("#tare-weight-handling-info").removeClass("d-none");
				}
				else
				{
					$("#tare-weight-handling-info").addClass("d-none");
				}

				$('#price').val(parseFloat(productDetails.last_price).toLocaleString({ minimumFractionDigits: 2, maximumFractionDigits: 2 }));
				if (Grocy.FeatureFlags.GROCY_FEATURE_FLAG_STOCK_PRICE_TRACKING) {
					Grocy.Components.ShoppingLocationPicker.SetId(productDetails.last_shopping_location_id);
				}
				if (Grocy.FeatureFlags.GROCY_FEATURE_FLAG_STOCK_LOCATION_TRACKING)
				{
					Grocy.Components.LocationPicker.SetId(productDetails.location.id);
				}

				$('#new_amount').val(productDetails.stock_amount);
				$('#new_amount').focus();
				$('#new_amount').trigger('keyup');
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
					if (Grocy.FeatureFlags.GROCY_FEATURE_FLAG_STOCK_LOCATION_TRACKING)
					{
						Grocy.Components.LocationPicker.GetInputElement().attr('required', '');
					}
				}
				else if (newAmount < productStockAmount + containerWeight)
				{
					$('#inventory-change-info').text(__t('This means %s will be removed from stock', estimatedBookingAmount.toLocaleString() + ' ' + __n(estimatedBookingAmount, productDetails.quantity_unit_stock.name, productDetails.quantity_unit_stock.name_plural)));
					Grocy.Components.DateTimePicker.GetInputElement().removeAttr('required');
					if (Grocy.FeatureFlags.GROCY_FEATURE_FLAG_STOCK_LOCATION_TRACKING)
					{
						Grocy.Components.LocationPicker.GetInputElement().removeAttr('required');
					}
				}

				if (!Grocy.FeatureFlags.GROCY_FEATURE_FLAG_STOCK_BEST_BEFORE_DATE_TRACKING)
				{
					Grocy.Components.DateTimePicker.GetInputElement().removeAttr('required');
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

function UndoStockTransaction(transactionId)
{
	Grocy.Api.Post('stock/transactions/' + transactionId.toString() + '/undo', { },
		function (result)
		{
			toastr.success(__t("Transaction successfully undone"));
		},
		function (xhr)
		{
			console.error(xhr);
		}
	);
};
