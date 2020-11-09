$('#save-purchase-button').on('click', function(e)
{
	e.preventDefault();

	var jsonForm = $('#purchase-form').serializeJSON();
	jsonForm.qu_factor_purchase_to_stock = $("#qu_id option:selected").attr("data-qu-factor");

	Grocy.FrontendHelpers.BeginUiBusy("purchase-form");

	Grocy.Api.Get('stock/products/' + jsonForm.product_id,
		function(productDetails)
		{
			var jsonData = {};
			jsonData.amount = jsonForm.amount;

			if (!Grocy.FeatureFlags.GROCY_FEATURE_FLAG_STOCK_PRICE_TRACKING)
			{
				jsonData.price = 0;
			} else
			{
				// price is saved as 1 QU to stock
				var price = parseFloat(jsonForm.price / jsonForm.qu_factor_purchase_to_stock).toFixed(Grocy.UserSettings.stock_decimal_places_prices);

				if ($("input[name='price-type']:checked").val() == "total-price")
				{
					price = parseFloat(price / jsonForm.amount).toFixed(Grocy.UserSettings.stock_decimal_places_prices);
				}
				jsonData.price = price;
			}

			if (Grocy.UserSettings.show_purchased_date_on_purchase)
			{
				jsonData.purchased_date = Grocy.Components.DateTimePicker2.GetValue();
			}

			if (Grocy.Components.DateTimePicker)
			{
				jsonData.best_before_date = Grocy.Components.DateTimePicker.GetValue();
			}
			else
			{
				jsonData.best_before_date = null;
			}

			if (Grocy.FeatureFlags.GROCY_FEATURE_FLAG_STOCK_PRICE_TRACKING)
			{
				jsonData.shopping_location_id = Grocy.Components.ShoppingLocationPicker.GetValue();
			}
			if (Grocy.FeatureFlags.GROCY_FEATURE_FLAG_STOCK_LOCATION_TRACKING)
			{
				jsonData.location_id = Grocy.Components.LocationPicker.GetValue();
			}
			jsonData.qu_factor_purchase_to_stock = jsonForm.qu_factor_purchase_to_stock;

			Grocy.Api.Post('stock/products/' + jsonForm.product_id + '/add', jsonData,
				function(result)
				{
					if (BoolVal(Grocy.UserSettings.scan_mode_purchase_enabled))
					{
						Grocy.UISound.Success();
					}

					var addBarcode = GetUriParam('addbarcodetoselection');
					if (addBarcode !== undefined)
					{
						var jsonDataBarcode = {};
						jsonDataBarcode.barcode = addBarcode;
						jsonDataBarcode.product_id = jsonForm.product_id;
						jsonDataBarcode.qu_factor_purchase_to_stock = jsonForm.qu_factor_purchase_to_stock;
						jsonDataBarcode.shopping_location_id = jsonForm.shopping_location_id;

						Grocy.Api.Post('objects/product_barcodes', jsonDataBarcode,
							function(result)
							{
								$("#flow-info-addbarcodetoselection").addClass("d-none");
								$('#barcode-lookup-disabled-hint').addClass('d-none');
								window.history.replaceState({}, document.title, U("/purchase"));
							},
							function(xhr)
							{
								Grocy.FrontendHelpers.EndUiBusy("purchase-form");
								Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response);
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

						Grocy.Components.ProductAmountPicker.Reset();
						$("#display_amount").attr("min", "1");
						$("#display_amount").attr("step", "1");
						$("#display_amount").parent().find(".invalid-feedback").text(__t('The amount cannot be lower than %s', '1'));
						$('#display_amount').val(parseFloat(Grocy.UserSettings.stock_default_purchase_amount).toLocaleString({ minimumFractionDigits: 0, maximumFractionDigits: Grocy.UserSettings.stock_decimal_places_amounts }));
						$('#price').val('');
						$("#tare-weight-handling-info").addClass("d-none");
						if (Grocy.FeatureFlags.GROCY_FEATURE_FLAG_STOCK_LOCATION_TRACKING)
						{
							Grocy.Components.LocationPicker.Clear();
						}
						if (Grocy.FeatureFlags.GROCY_FEATURE_FLAG_STOCK_BEST_BEFORE_DATE_TRACKING)
						{
							Grocy.Components.DateTimePicker.Clear();
						}
						Grocy.Components.ProductPicker.SetValue('');
						if (Grocy.FeatureFlags.GROCY_FEATURE_FLAG_STOCK_PRICE_TRACKING)
						{
							Grocy.Components.ShoppingLocationPicker.SetValue('');
						}
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
		if (BoolVal(Grocy.UserSettings.scan_mode_purchase_enabled))
		{
			Grocy.UISound.BarcodeScannerBeep();
		}

		var productId = $(e.target).val();

		if (productId)
		{
			Grocy.Components.ProductCard.Refresh(productId);

			if (document.getElementById("product_id").getAttribute("barcode") != "null")
			{
				Grocy.Api.Get('productbarcodedetails/' + document.getElementById("product_id").getAttribute("barcode"),
					function(resultBarcode)
					{
						if (resultBarcode != null)
						{
							$('#product_id').attr("barcode-qu-factor-purchase-to-stock", resultBarcode.qu_factor_purchase_to_stock);
							$('#product_id').attr("barcode-shopping-location-id", resultBarcode.shopping_location_id);
						}
						else
						{
							$('#product_id').attr("barcode-qu-factor-purchase-to-stock", "null");
							$('#product_id').attr("barcode-shopping-location-id", "null");
						}
					},
					function(xhr)
					{
						console.error(xhr);
					}
				);
			}
			else
			{
				$('#product_id').attr("barcode-qu-factor-purchase-to-stock", "null");
				$('#product_id').attr("barcode-shopping-location-id", "null");
			}

			Grocy.Api.Get('stock/products/' + productId,
				function(productDetails)
				{
					Grocy.Components.ProductAmountPicker.Reload(productDetails.product.id, productDetails.quantity_unit_stock.id);
					Grocy.Components.ProductAmountPicker.SetQuantityUnit(productDetails.quantity_unit_purchase.id);

					var qu_factor_purchase_to_stock = null;
					var barcode_shopping_location_id = null;

					if (document.getElementById("product_id").getAttribute("barcode") != "null" && document.getElementById("product_id").getAttribute("barcode-qu-factor-purchase-to-stock") != "null")
					{
						qu_factor_purchase_to_stock = document.getElementById("product_id").getAttribute("barcode-qu-factor-purchase-to-stock");
						barcode_shopping_location_id = document.getElementById("product_id").getAttribute("barcode-shopping-location-id");
					}
					else
					{
						if (productDetails.last_qu_factor_purchase_to_stock != null)
						{
							qu_factor_purchase_to_stock = productDetails.last_qu_factor_purchase_to_stock;
						}
						else
						{
							qu_factor_purchase_to_stock = productDetails.product.qu_factor_purchase_to_stock;
						}
					}

					if (Grocy.FeatureFlags.GROCY_FEATURE_FLAG_STOCK_PRICE_TRACKING)
					{
						if (barcode_shopping_location_id != null)
						{
							Grocy.Components.ShoppingLocationPicker.SetId(barcode_shopping_location_id);
						}
						else if (productDetails.last_shopping_location_id != null)
						{
							Grocy.Components.ShoppingLocationPicker.SetId(productDetails.last_shopping_location_id);
						}
						else
						{
							Grocy.Components.ShoppingLocationPicker.SetId(productDetails.default_shopping_location_id);
						}
					}

					if (Grocy.FeatureFlags.GROCY_FEATURE_FLAG_STOCK_LOCATION_TRACKING)
					{
						Grocy.Components.LocationPicker.SetId(productDetails.location.id);
					}

					$('#price').val(parseFloat(productDetails.last_price * qu_factor_purchase_to_stock).toLocaleString({ minimumFractionDigits: 2, maximumFractionDigits: Grocy.UserSettings.stock_decimal_places_prices }));

					var priceTypeUnitPrice = $("#price-type-unit-price");
					var priceTypeUnitPriceLabel = $("[for=" + priceTypeUnitPrice.attr("id") + "]");
					priceTypeUnitPriceLabel.text(productDetails.quantity_unit_purchase.name + " price");

					refreshPriceHint();

					if (productDetails.product.allow_partial_units_in_stock == 1)
					{
						$("#display_amount").attr("min", "0." + "0".repeat(parseInt(Grocy.UserSettings.stock_decimal_places_amounts) - 1) + "1");
						$("#display_amount").attr("step", "." + "0".repeat(parseInt(Grocy.UserSettings.stock_decimal_places_amounts) - 1) + "1");
						$("#display_amount").parent().find(".invalid-feedback").text(__t('The amount cannot be lower than %s', "0." + "0".repeat(parseInt(Grocy.UserSettings.stock_decimal_places_amounts) - 1) + "1"));
					}
					else
					{
						$("#display_amount").attr("min", "1");
						$("#display_amount").attr("step", "1");
						$("#display_amount").parent().find(".invalid-feedback").text(__t('The amount cannot be lower than %s', '1'));
					}

					if (productDetails.product.enable_tare_weight_handling == 1)
					{
						var minAmount = parseFloat(productDetails.product.tare_weight) / qu_factor_purchase_to_stock + parseFloat(productDetails.stock_amount);
						$("#display_amount").attr("min", minAmount);
						$("#display_amount").attr("step", "." + "0".repeat(parseInt(Grocy.UserSettings.stock_decimal_places_amounts) - 1) + "1");
						$("#display_amount").parent().find(".invalid-feedback").text(__t('The amount cannot be lower than %s', minAmount.toLocaleString({ minimumFractionDigits: 0, maximumFractionDigits: Grocy.UserSettings.stock_decimal_places_amounts })));
						$("#tare-weight-handling-info").removeClass("d-none");
					}
					else
					{
						$("#tare-weight-handling-info").addClass("d-none");
					}

					if (Grocy.FeatureFlags.GROCY_FEATURE_FLAG_STOCK_BEST_BEFORE_DATE_TRACKING)
					{
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
						}
					}

					$("#display_amount").focus();

					Grocy.FrontendHelpers.ValidateForm('purchase-form');
					if (GetUriParam("flow") === "shoppinglistitemtostock" && BoolVal(Grocy.UserSettings.shopping_list_to_stock_workflow_auto_submit_when_prefilled) && document.getElementById("purchase-form").checkValidity() === true)
					{
						$("#save-purchase-button").click();
					}

					if (BoolVal(Grocy.UserSettings.scan_mode_purchase_enabled))
					{
						$("#display_amount").val(1);
						Grocy.FrontendHelpers.ValidateForm("purchase-form");
						if (document.getElementById("purchase-form").checkValidity() === true)
						{
							$('#save-purchase-button').click();
						}
						else
						{
							toastr.warning(__t("Scan mode is on but not all required fields could be populated automatically"));
							Grocy.UISound.Error();
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

$('#display_amount').val(parseFloat(Grocy.UserSettings.stock_default_purchase_amount).toLocaleString({ minimumFractionDigits: 0, maximumFractionDigits: Grocy.UserSettings.stock_decimal_places_amounts }));
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

$('#display_amount').on('focus', function(e)
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

$('#price').on('focus', function(e)
{
	$(this).select();
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

$('#price').on('change', function(e)
{
	refreshPriceHint();
});

$('#price-type-unit-price').on('change', function(e)
{
	refreshPriceHint();
});

$('#price-type-total-price').on('change', function(e)
{
	refreshPriceHint();
});

$('#display_amount').on('change', function(e)
{
	refreshPriceHint();
	Grocy.FrontendHelpers.ValidateForm('purchase-form');
});

if (GetUriParam("flow") === "shoppinglistitemtostock")
{
	$('#display_amount').val(parseFloat(GetUriParam("amount")).toLocaleString({ minimumFractionDigits: 0, maximumFractionDigits: Grocy.UserSettings.stock_decimal_places_amounts }));
}

function refreshPriceHint()
{
	if ($('#amount').val() == 0)
	{
		$('#price-hint').text("");
		return;
	}
	if ($('#price').val() == 0)
	{
		$('#price-hint').text("");
		return;
	}

	if ($("input[name='price-type']:checked").val() == "total-price")
	{
		var price = $('#price').val() / $("#qu_id option:selected").attr("data-qu-factor") / $('#amount').val();
		var quprice = $('#price').val() / $('#amount').val();

		if ($("#qu_id option:selected").attr("data-qu-factor") > 1)
		{
			$('#price-hint').text(__t('means %1$s per %2$s and %3$s per %4$s', price.toLocaleString({ minimumFractionDigits: 2, maximumFractionDigits: Grocy.UserSettings.stock_decimal_places_prices }), $("#qu_id").attr("data-destination-qu-name"), quprice.toLocaleString({ minimumFractionDigits: 2, maximumFractionDigits: Grocy.UserSettings.stock_decimal_places_prices }), document.getElementById("amount_qu_unit").getAttribute("quantity-unit-purchase-name")));
		}
		else
		{
			$('#price-hint').text(__t('means %1$s per %2$s', price.toLocaleString({ minimumFractionDigits: 2, maximumFractionDigits: Grocy.UserSettings.stock_decimal_places_prices }), $("#qu_id").attr("data-destination-qu-name")));
		}
	}
	else
	{
		if ($("#qu_id option:selected").attr("data-qu-factor") > 1)
		{
			var price = $('#price').val() / $("#qu_id option:selected").attr("data-qu-factor");
			$('#price-hint').text(__t('means %1$s per %2$s', price.toLocaleString({ minimumFractionDigits: 2, maximumFractionDigits: Grocy.UserSettings.stock_decimal_places_prices }), $("#qu_id").attr("data-destination-qu-name")));
		}
		else
		{
			$('#price-hint').text("");
		}
	}
};

function UndoStockBooking(bookingId)
{
	Grocy.Api.Post('stock/bookings/' + bookingId.toString() + '/undo', {},
		function(result)
		{
			toastr.success(__t("Booking successfully undone"));

			Grocy.Api.Get('stock/bookings/' + bookingId.toString(),
				function(result)
				{
					window.postMessage(WindowMessageBag("ProductChanged", result.product_id), Grocy.BaseUrl);
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
};

function UndoStockTransaction(transactionId)
{
	Grocy.Api.Post('stock/transactions/' + transactionId.toString() + '/undo', {},
		function(result)
		{
			toastr.success(__t("Transaction successfully undone"));

			Grocy.Api.Get('stock/transactions/' + transactionId.toString(),
				function(result)
				{
					window.postMessage(WindowMessageBag("ProductChanged", result[0].product_id), Grocy.BaseUrl);
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
};

$("#scan-mode").on("change", function(e)
{
	if ($(this).prop("checked"))
	{
		Grocy.UISound.AskForPermission();
	}
});

$("#scan-mode-button").on("click", function(e)
{
	document.activeElement.blur();
	$("#scan-mode").click();
	$("#scan-mode-button").toggleClass("btn-success").toggleClass("btn-danger");
	if ($("#scan-mode").prop("checked"))
	{
		$("#scan-mode-status").text(__t("on"));
	}
	else
	{
		$("#scan-mode-status").text(__t("off"));
	}
});
