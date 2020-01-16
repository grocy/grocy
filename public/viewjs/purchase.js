$('#save-purchase-button').on('click', function(e)
{
	e.preventDefault();

	var jsonForm = $('#purchase-form').serializeJSON();
	Grocy.FrontendHelpers.BeginUiBusy("purchase-form");

	Grocy.Api.Get('stock/products/' + jsonForm.product_id,
		function(productDetails)
		{
			var amount = jsonForm.amount * productDetails.product.qu_factor_purchase_to_stock;

			var price = "";
			if (!jsonForm.price.toString().isEmpty())
			{
				price = parseFloat(jsonForm.price).toFixed(2);

				if ($("input[name='price-type']:checked").val() == "total-price")
				{
					price = price / amount;
				}
			}

			if (!Grocy.FeatureFlags.GROCY_FEATURE_FLAG_STOCK_PRICE_TRACKING)
			{
				price = 0;
			}

			var jsonData = {};
			jsonData.amount = amount;
			jsonData.best_before_date = Grocy.Components.DateTimePicker.GetValue();
			jsonData.price = price;
			if (Grocy.FeatureFlags.GROCY_FEATURE_FLAG_STOCK_LOCATION_TRACKING)
			{
				jsonData.location_id = Grocy.Components.LocationPicker.GetValue();
			}
			else
			{
				jsonData.location_id = 1;
			}

			Grocy.Api.Post('stock/products/' + jsonForm.product_id + '/add', jsonData,
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
								window.history.replaceState({ }, document.title, U("/purchase"));
							},
							function(xhr)
							{
								Grocy.FrontendHelpers.EndUiBusy("purchase-form");
								console.error(xhr);
							}
						);
					}

					var successMessage = __t('Added %1$s of %2$s to stock', result.amount + " " + __n(result.amount, productDetails.quantity_unit_stock.name, productDetails.quantity_unit_stock.name_plural), productDetails.product.name) + '<br><a class="btn btn-secondary btn-sm mt-2" href="#" onclick="UndoStockTransaction(\'' + result.transaction_id + '\')"><i class="fas fa-undo"></i> ' + __t("Undo") + '</a>';

					if (GetUriParam("embedded") !== undefined)
					{
						window.parent.postMessage(WindowMessageBag("ProductChanged", jsonForm.product_id), Grocy.BaseUrl);
						window.parent.postMessage(WindowMessageBag("AfterItemAdded", GetUriParam("listitemid")), Grocy.BaseUrl);
						window.parent.postMessage(WindowMessageBag("ShowSuccessMessage", successMessage), Grocy.BaseUrl);
						window.parent.postMessage(WindowMessageBag("Ready"), Grocy.BaseUrl);
						window.parent.postMessage(WindowMessageBag("CloseAllModals"), Grocy.BaseUrl);
					}
					else
					{
						Grocy.FrontendHelpers.EndUiBusy("purchase-form");
						toastr.success(successMessage);

						$("#amount").attr("min", "1");
						$("#amount").attr("step", "1");
						$("#amount").parent().find(".invalid-feedback").text(__t('The amount cannot be lower than %s', '1'));
						$('#amount').val(Grocy.UserSettings.stock_default_purchase_amount);
						$('#price').val('');
						$('#amount_qu_unit').text("");
						$("#tare-weight-handling-info").addClass("d-none");
						if (Grocy.FeatureFlags.GROCY_FEATURE_FLAG_STOCK_LOCATION_TRACKING)
						{
							Grocy.Components.LocationPicker.Clear();
						}
						Grocy.Components.DateTimePicker.Clear();
						Grocy.Components.ProductPicker.SetValue('');
						Grocy.Components.ProductPicker.GetInputElement().focus();
						Grocy.Components.ProductCard.Refresh(jsonForm.product_id);
						Grocy.FrontendHelpers.ValidateForm('purchase-form');
					}
				},
				function(xhr)
				{
					Grocy.FrontendHelpers.EndUiBusy("purchase-form");
					console.error(xhr);
				}
			);
		},
		function(xhr)
		{
			Grocy.FrontendHelpers.EndUiBusy("purchase-form");
			console.error(xhr);
		}
	);
});

if (Grocy.Components.ProductPicker !== undefined)
{
	Grocy.Components.ProductPicker.GetPicker().on('change', function(e)
	{
		var productId = $(e.target).val();

		if (productId)
		{
			Grocy.Components.ProductCard.Refresh(productId);

			Grocy.Api.Get('stock/products/' + productId,
				function (productDetails)
				{
					$('#price').val(productDetails.last_price);
					if (Grocy.FeatureFlags.GROCY_FEATURE_FLAG_STOCK_LOCATION_TRACKING)
					{
						Grocy.Components.LocationPicker.SetId(productDetails.location.id);
					}

					if (productDetails.product.qu_id_purchase === productDetails.product.qu_id_stock)
					{
						$('#amount_qu_unit').text(productDetails.quantity_unit_purchase.name);
					}
					else
					{
						$('#amount_qu_unit').text(productDetails.quantity_unit_purchase.name + " (" + __t("will be multiplied a factor of %1$s to get %2$s", parseInt(productDetails.product.qu_factor_purchase_to_stock).toString(), __n(2, productDetails.quantity_unit_stock.name, productDetails.quantity_unit_stock.name_plural)) + ")");
					}

					if (productDetails.product.allow_partial_units_in_stock == 1)
					{
						$("#amount").attr("min", "0.01");
						$("#amount").attr("step", "0.01");
						$("#amount").parent().find(".invalid-feedback").text(__t('The amount cannot be lower than %s', 0.01.toLocaleString()));
					}
					else
					{
						$("#amount").attr("min", "1");
						$("#amount").attr("step", "1");
						$("#amount").parent().find(".invalid-feedback").text(__t('The amount cannot be lower than %s', '1'));
					}

					if (productDetails.product.enable_tare_weight_handling == 1)
					{
						var minAmount = parseFloat(productDetails.product.tare_weight) / productDetails.product.qu_factor_purchase_to_stock + parseFloat(productDetails.stock_amount);
						$("#amount").attr("min", minAmount);
						$("#amount").attr("step", "0.0001");
						$("#amount").parent().find(".invalid-feedback").text(__t('The amount cannot be lower than %s', minAmount.toLocaleString()));
						$("#tare-weight-handling-info").removeClass("d-none");
					}
					else
					{
						$("#tare-weight-handling-info").addClass("d-none");
					}

					if (productDetails.product.default_best_before_days.toString() !== '0')
					{
						if (productDetails.product.default_best_before_days == -1)
						{
							if (!$("#datetimepicker-shortcut").is(":checked"))
							{
								$("#datetimepicker-shortcut").click();
							}
						}
						else
						{
							Grocy.Components.DateTimePicker.SetValue(moment().add(productDetails.product.default_best_before_days, 'days').format('YYYY-MM-DD'));
						}
						$('#amount').focus();

						Grocy.FrontendHelpers.ValidateForm('purchase-form');
						if (GetUriParam("flow") === "shoppinglistitemtostock" && BoolVal(Grocy.UserSettings.shopping_list_to_stock_workflow_auto_submit_when_prefilled) && document.getElementById("purchase-form").checkValidity() === true)
						{
							$("#save-purchase-button").click();
						}
					}
					else
					{
						if (Grocy.FeatureFlags.GROCY_FEATURE_FLAG_STOCK_BEST_BEFORE_DATE_TRACKING)
						{
							Grocy.Components.DateTimePicker.GetInputElement().focus();
						}
						else
						{
							Grocy.Components.DateTimePicker.SetValue(moment().format('YYYY-MM-DD'));
							$('#amount').focus();
						}
					}
				},
				function(xhr)
				{
					console.error(xhr);
				}
			);
		}
	});
}

$('#amount').val(Grocy.UserSettings.stock_default_purchase_amount);
Grocy.FrontendHelpers.ValidateForm('purchase-form');

if (Grocy.Components.ProductPicker)
{
	if (Grocy.Components.ProductPicker.InProductAddWorkflow() === false)
	{
		Grocy.Components.ProductPicker.GetInputElement().focus();
	}
	else
	{
		Grocy.Components.ProductPicker.GetPicker().trigger('change');
	}
}

$('#amount').on('focus', function(e)
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

$('#purchase-form input').keyup(function(event)
{
	Grocy.FrontendHelpers.ValidateForm('purchase-form');
});

$('#purchase-form input').keydown(function(event)
{
	if (event.keyCode === 13) //Enter
	{
		event.preventDefault();

		if (document.getElementById('purchase-form').checkValidity() === false) //There is at least one validation error
		{
			return false;
		}
		else
		{
			$('#save-purchase-button').click();
		}
	}
});

if (Grocy.Components.DateTimePicker)
{
	Grocy.Components.DateTimePicker.GetInputElement().on('change', function(e)
	{
		Grocy.FrontendHelpers.ValidateForm('purchase-form');
	});

	Grocy.Components.DateTimePicker.GetInputElement().on('keypress', function(e)
	{
		Grocy.FrontendHelpers.ValidateForm('purchase-form');
	});
}

$('#amount').on('change', function(e)
{
	Grocy.FrontendHelpers.ValidateForm('purchase-form');
});

if (GetUriParam("flow") === "shoppinglistitemtostock")
{
	$('#amount').val(GetUriParam("amount"));
}

function UndoStockBooking(bookingId)
{
	Grocy.Api.Post('stock/bookings/' + bookingId.toString() + '/undo', { },
		function(result)
		{
			toastr.success(__t("Booking successfully undone"));

			Grocy.Api.Get('stock/bookings/' + bookingId.toString(),
				function(result)
				{
					window.postMessage(WindowMessageBag("ProductChanged", result.product_id), Grocy.BaseUrl);
				},
				function (xhr)
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
};

function UndoStockTransaction(transactionId)
{
	Grocy.Api.Post('stock/transactions/' + transactionId.toString() + '/undo', { },
		function(result)
		{
			toastr.success(__t("Transaction successfully undone"));

			Grocy.Api.Get('stock/transactions/' + transactionId.toString(),
				function(result)
				{
					window.postMessage(WindowMessageBag("ProductChanged", result[0].product_id), Grocy.BaseUrl);
				},
				function (xhr)
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
};
