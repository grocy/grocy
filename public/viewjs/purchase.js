var CurrentProductDetails;

$('#save-purchase-button').on('click', function(e)
{
	e.preventDefault();

	if (!Grocy.FrontendHelpers.ValidateForm("purchase-form", true))
	{
		return;
	}

	if ($(".combobox-menu-visible").length)
	{
		return;
	}

	if ($(".combobox-menu-visible").length)
	{
		return;
	}

	var jsonForm = $('#purchase-form').serializeJSON();

	Grocy.FrontendHelpers.BeginUiBusy("purchase-form");

	Grocy.Api.Get('stock/products/' + jsonForm.product_id,
		function(productDetails)
		{
			var jsonData = {};
			jsonData.amount = jsonForm.amount;
			jsonData.note = jsonForm.note;
			jsonData.stock_label_type = jsonForm.stock_label_type;

			if (!Grocy.FeatureFlags.GROCY_FEATURE_FLAG_STOCK_PRICE_TRACKING)
			{
				jsonData.price = 0;
			}
			else
			{
				var amount = jsonForm.display_amount;
				if (BoolVal(productDetails.product.enable_tare_weight_handling))
				{
					amount -= parseFloat(productDetails.product.tare_weight);
				}

				var price = parseFloat(jsonForm.price * $("#qu_id option:selected").attr("data-qu-factor")).toFixed(Grocy.UserSettings.stock_decimal_places_prices_input);
				if ($("input[name='price-type']:checked").val() == "total-price")
				{
					price = parseFloat(price / amount).toFixed(Grocy.UserSettings.stock_decimal_places_prices_input);
				}

				jsonData.price = price;
			}

			if (BoolVal(Grocy.UserSettings.show_purchased_date_on_purchase))
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

			Grocy.Api.Post('stock/products/' + jsonForm.product_id + '/add', jsonData,
				function(result)
				{
					if ($("#purchase-form").hasAttr("data-used-barcode"))
					{
						Grocy.Api.Put('objects/product_barcodes/' + $("#purchase-form").attr("data-used-barcode"), { last_price: $("#price").val() },
							function(result)
							{ },
							function(xhr)
							{ }
						);
					}

					if (BoolVal(Grocy.UserSettings.scan_mode_purchase_enabled))
					{
						Grocy.UISound.Success();
					}

					if (GetUriParam("flow") == "InplaceAddBarcodeToExistingProduct")
					{
						var jsonDataBarcode = {};
						jsonDataBarcode.barcode = GetUriParam("barcode");
						jsonDataBarcode.product_id = jsonForm.product_id;
						jsonDataBarcode.shopping_location_id = jsonForm.shopping_location_id;

						Grocy.Api.Post('objects/product_barcodes', jsonDataBarcode,
							function(result)
							{
								$("#flow-info-InplaceAddBarcodeToExistingProduct").addClass("d-none");
								$('#barcode-lookup-disabled-hint').addClass('d-none');
								$('#barcode-lookup-hint').removeClass('d-none');
								window.history.replaceState({}, document.title, U("/purchase"));
							},
							function(xhr)
							{
								Grocy.FrontendHelpers.EndUiBusy("purchase-form");
								Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response);
							}
						);
					}

					var amountMessage = parseFloat(jsonForm.amount).toLocaleString({ minimumFractionDigits: 0, maximumFractionDigits: Grocy.UserSettings.stock_decimal_places_amounts });
					if (BoolVal(productDetails.product.enable_tare_weight_handling))
					{
						amountMessage = parseFloat(jsonForm.amount) - parseFloat(productDetails.stock_amount) - parseFloat(productDetails.product.tare_weight);
					}
					var successMessage = __t('Added %1$s of %2$s to stock', amountMessage + " " + __n(amountMessage, productDetails.quantity_unit_stock.name, productDetails.quantity_unit_stock.name_plural, true), productDetails.product.name) + '<br><a class="btn btn-secondary btn-sm mt-2" href="#" onclick="UndoStockTransaction(\'' + result[0].transaction_id + '\')"><i class="fa-solid fa-undo"></i> ' + __t("Undo") + '</a>';

					if (Grocy.FeatureFlags.GROCY_FEATURE_FLAG_LABEL_PRINTER)
					{
						if (Grocy.Webhooks.labelprinter !== undefined)
						{
							if (jsonForm.stock_label_type == 1) // Single label
							{
								var webhookData = {};
								webhookData.product = productDetails.product.name;
								webhookData.grocycode = 'grcy:p:' + jsonForm.product_id + ":" + result[0].stock_id;
								if (Grocy.FeatureFlags.GROCY_FEATURE_FLAG_STOCK_BEST_BEFORE_DATE_TRACKING)
								{
									webhookData.due_date = __t('DD') + ': ' + result[0].best_before_date;
								}

								Grocy.FrontendHelpers.RunWebhook(Grocy.Webhooks.labelprinter, webhookData);
							}
							else if (jsonForm.stock_label_type == 2) // Label per unit
							{
								Grocy.Api.Get('stock/transactions/' + result[0].transaction_id,
									function(stockEntries)
									{
										stockEntries.forEach(stockEntry =>
										{
											var webhookData = {};
											webhookData.product = productDetails.product.name;
											webhookData.grocycode = 'grcy:p:' + jsonForm.product_id + ":" + stockEntry.stock_id;
											if (Grocy.FeatureFlags.GROCY_FEATURE_FLAG_STOCK_BEST_BEFORE_DATE_TRACKING)
											{
												webhookData.due_date = __t('DD') + ': ' + result[0].best_before_date;
											}

											Grocy.FrontendHelpers.RunWebhook(Grocy.Webhooks.labelprinter, webhookData);
										});
									},
									function(xhr)
									{
										console.error(xhr);
									}
								);
							}
						}
					}

					Grocy.EditObjectId = result[0].transaction_id;
					if (GetUriParam("embedded") !== undefined)
					{
						Grocy.Components.UserfieldsForm.Save(function()
						{
							window.parent.postMessage(WindowMessageBag("ProductChanged", jsonForm.product_id), Grocy.BaseUrl);
							window.parent.postMessage(WindowMessageBag("AfterItemAdded", GetUriParam("listitemid")), Grocy.BaseUrl);
							window.parent.postMessage(WindowMessageBag("ShowSuccessMessage", successMessage), Grocy.BaseUrl);
							window.parent.postMessage(WindowMessageBag("Ready"), Grocy.BaseUrl);
							window.parent.postMessage(WindowMessageBag("CloseAllModals"), Grocy.BaseUrl);
						});
					}
					else
					{
						Grocy.Components.UserfieldsForm.Save(function()
						{
							Grocy.FrontendHelpers.EndUiBusy("purchase-form");
							toastr.success(successMessage);
							Grocy.Components.ProductPicker.FinishFlow();

							if (Grocy.FeatureFlags.GROCY_FEATURE_FLAG_STOCK_BEST_BEFORE_DATE_TRACKING && BoolVal(Grocy.UserSettings.show_warning_on_purchase_when_due_date_is_earlier_than_next))
							{
								if (moment(jsonData.best_before_date).isBefore(CurrentProductDetails.next_due_date))
								{
									toastr.warning(__t("This is due earlier than already in-stock items"));
								}
							}

							Grocy.Components.ProductAmountPicker.Reset();
							$("#purchase-form").removeAttr("data-used-barcode");
							$("#display_amount").attr("min", Grocy.DefaultMinAmount);
							$('#display_amount').val(parseFloat(Grocy.UserSettings.stock_default_purchase_amount));
							$(".input-group-productamountpicker").trigger("change");
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
							if (Grocy.FeatureFlags.GROCY_FEATURE_FLAG_LABEL_PRINTER)
							{
								$("#stock_label_type").val(0);
							}

							$('#price-hint').text("");
							$('#note').val("");
							var priceTypeUnitPrice = $("#price-type-unit-price");
							var priceTypeUnitPriceLabel = $("[for=" + priceTypeUnitPrice.attr("id") + "]");
							priceTypeUnitPriceLabel.text(__t("Unit price"));
							Grocy.Components.UserfieldsForm.Clear();

							Grocy.FrontendHelpers.ValidateForm('purchase-form');
						});
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

			Grocy.Api.Get('stock/products/' + productId,
				function(productDetails)
				{
					CurrentProductDetails = productDetails;

					Grocy.Components.ProductAmountPicker.Reload(productDetails.product.id, productDetails.quantity_unit_stock.id);
					if (productDetails.product.enable_tare_weight_handling == 1)
					{
						Grocy.Components.ProductAmountPicker.SetQuantityUnit(productDetails.quantity_unit_stock.id);
						$("#qu_id").attr("disabled", "");
					}
					else
					{
						Grocy.Components.ProductAmountPicker.SetQuantityUnit(productDetails.default_quantity_unit_purchase.id);
					}
					$('#display_amount').val(parseFloat(Grocy.UserSettings.stock_default_purchase_amount));
					$(".input-group-productamountpicker").trigger("change");

					if (GetUriParam("flow") === "shoppinglistitemtostock")
					{
						Grocy.Components.ProductAmountPicker.SetQuantityUnit(GetUriParam("quId"));
						$('#display_amount').val(parseFloat(GetUriParam("amount") * $("#qu_id option:selected").attr("data-qu-factor")));
					}

					$(".input-group-productamountpicker").trigger("change");

					if (Grocy.FeatureFlags.GROCY_FEATURE_FLAG_STOCK_PRICE_TRACKING)
					{
						if (productDetails.last_shopping_location_id != null)
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

					if (productDetails.last_price == null || productDetails.last_price == 0)
					{
						$("#price").val("")
					}
					else
					{
						$('#price').val(parseFloat(productDetails.last_price / $("#qu_id option:selected").attr("data-qu-factor")));
					}

					var priceTypeUnitPrice = $("#price-type-unit-price");
					var priceTypeUnitPriceLabel = $("[for=" + priceTypeUnitPrice.attr("id") + "]");
					priceTypeUnitPriceLabel.text($("#qu_id option:selected").text() + " " + __t("price"));

					refreshPriceHint();

					if (productDetails.product.enable_tare_weight_handling == 1)
					{
						var minAmount = parseFloat(productDetails.product.tare_weight) / $("#qu_id option:selected").attr("data-qu-factor") + parseFloat(productDetails.stock_amount);
						$("#display_amount").attr("min", minAmount);
						$("#tare-weight-handling-info").removeClass("d-none");
					}
					else
					{
						$("#display_amount").attr("min", Grocy.DefaultMinAmount);
						$("#tare-weight-handling-info").addClass("d-none");
					}

					PrefillBestBeforeDate(productDetails.product, productDetails.location);

					if (Grocy.FeatureFlags.GROCY_FEATURE_FLAG_LABEL_PRINTER)
					{
						$("#stock_label_type").val(productDetails.product.default_stock_label_type);
					}

					$("#display_amount").focus();

					Grocy.FrontendHelpers.ValidateForm('purchase-form');
					if (GetUriParam("flow") === "shoppinglistitemtostock" && BoolVal(Grocy.UserSettings.shopping_list_to_stock_workflow_auto_submit_when_prefilled) && Grocy.FrontendHelpers.ValidateForm("purchase-form"))
					{
						$("#save-purchase-button").click();
					}

					RefreshLocaleNumberInput();

					if (document.getElementById("product_id").getAttribute("barcode") != "null")
					{
						Grocy.Api.Get('objects/product_barcodes?query[]=barcode=' + document.getElementById("product_id").getAttribute("barcode"),
							function(barcodeResult)
							{
								if (barcodeResult != null)
								{
									var barcode = barcodeResult[0];
									$("#purchase-form").attr("data-used-barcode", barcode.id);

									if (barcode != null)
									{
										if (barcode.amount != null && !barcode.amount.isEmpty())
										{
											$("#display_amount").val(barcode.amount);
											$("#display_amount").select();
										}

										if (barcode.qu_id != null && !barcode.qu_id.isEmpty())
										{
											Grocy.Components.ProductAmountPicker.SetQuantityUnit(barcode.qu_id);
										}

										if (Grocy.FeatureFlags.GROCY_FEATURE_FLAG_STOCK_PRICE_TRACKING && barcode.shopping_location_id != null)
										{
											Grocy.Components.ShoppingLocationPicker.SetId(barcode.shopping_location_id);
										}

										if (barcode.last_price != null && !barcode.last_price.isEmpty())
										{
											$("#price").val(barcode.last_price);
											$("#price-type-total-price").click();
										}

										$(".input-group-productamountpicker").trigger("change");
										Grocy.FrontendHelpers.ValidateForm('purchase-form');
										RefreshLocaleNumberInput();
									}
								}

								ScanModeSubmit(false);
							},
							function(xhr)
							{
								console.error(xhr);
							}
						);
					}
					else
					{
						$("#purchase-form").removeAttr("data-used-barcode");
						ScanModeSubmit();
					}

					$('#display_amount').trigger("keyup");
				},
				function(xhr)
				{
					console.error(xhr);
				}
			);
		}
	});
}

function PrefillBestBeforeDate(product, location)
{
	if (location == null)
	{
		location = {}
	}

	if (Grocy.FeatureFlags.GROCY_FEATURE_FLAG_STOCK_BEST_BEFORE_DATE_TRACKING)
	{
		var dueDays;
		if (Grocy.FeatureFlags.GROCY_FEATURE_FLAG_STOCK_PRODUCT_FREEZING && BoolVal(location.is_freezer))
		{
			dueDays = product.default_best_before_days_after_freezing;
		}
		else
		{
			dueDays = product.default_best_before_days;
		}

		dueDays = parseFloat(dueDays);
		if (dueDays != 0)
		{
			if (dueDays == -1)
			{
				if (!$("#datetimepicker-shortcut").is(":checked"))
				{
					$("#datetimepicker-shortcut").click();
				}
			}
			else
			{
				Grocy.Components.DateTimePicker.SetValue(moment().add(dueDays, 'days').format('YYYY-MM-DD'));
			}
		}
	}
}

if (Grocy.Components.LocationPicker !== undefined)
{
	Grocy.Components.LocationPicker.GetPicker().on('change', function(e)
	{
		if (Grocy.FeatureFlags.GROCY_FEATURE_FLAG_STOCK_PRODUCT_FREEZING)
		{
			Grocy.Api.Get('objects/locations/' + Grocy.Components.LocationPicker.GetValue(),
				function(location)
				{
					PrefillBestBeforeDate(CurrentProductDetails.product, location);
				},
				function(xhr)
				{ }
			);
		}
	});
}

$('#display_amount').val(parseFloat(Grocy.UserSettings.stock_default_purchase_amount));
RefreshLocaleNumberInput();
$(".input-group-productamountpicker").trigger("change");
Grocy.FrontendHelpers.ValidateForm('purchase-form');

if (Grocy.Components.ProductPicker)
{
	if (Grocy.Components.ProductPicker.InAnyFlow() === false && GetUriParam("embedded") === undefined)
	{
		Grocy.Components.ProductPicker.GetInputElement().focus();
	}
	else
	{
		Grocy.Components.ProductPicker.GetPicker().trigger('change');

		if (Grocy.Components.ProductPicker.InProductModifyWorkflow())
		{
			Grocy.Components.ProductPicker.GetInputElement().focus();
		}
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
	if (event.keyCode === 13) // Enter
	{
		event.preventDefault();

		if (!Grocy.FrontendHelpers.ValidateForm('purchase-form'))
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

if (Grocy.Components.DateTimePicker2)
{
	Grocy.Components.DateTimePicker2.GetInputElement().on('change', function(e)
	{
		Grocy.FrontendHelpers.ValidateForm('purchase-form');
	});

	Grocy.Components.DateTimePicker2.GetInputElement().on('keypress', function(e)
	{
		Grocy.FrontendHelpers.ValidateForm('purchase-form');
	});

	Grocy.Components.DateTimePicker2.GetInputElement().trigger("input");
}

$('#price').on('keyup', function(e)
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

function refreshPriceHint()
{
	if ($('#amount').val() == 0 || $('#price').val() == 0)
	{
		$('#price-hint').text("");
		return;
	}

	if ($("input[name='price-type']:checked").val() == "total-price" || $("#qu_id").attr("data-destination-qu-name") != $("#qu_id option:selected").text())
	{
		var amount = $('#display_amount').val();
		if (BoolVal(CurrentProductDetails.product.enable_tare_weight_handling))
		{
			amount -= parseFloat(CurrentProductDetails.product.tare_weight);
		}

		var price = parseFloat($('#price').val() * $("#qu_id option:selected").attr("data-qu-factor")).toFixed(Grocy.UserSettings.stock_decimal_places_prices_input);
		if ($("input[name='price-type']:checked").val() == "total-price")
		{
			price = parseFloat(price / amount).toFixed(Grocy.UserSettings.stock_decimal_places_prices_input);
		}

		$('#price-hint').text(__t('means %1$s per %2$s', price.toLocaleString(undefined, { style: "currency", currency: Grocy.Currency, minimumFractionDigits: Grocy.UserSettings.stock_decimal_places_prices_display, maximumFractionDigits: Grocy.UserSettings.stock_decimal_places_prices_display }), $("#qu_id").attr("data-destination-qu-name")));
	}
	else
	{
		$('#price-hint').text("");
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

$('#qu_id').on('change', function(e)
{
	var priceTypeUnitPrice = $("#price-type-unit-price");
	var priceTypeUnitPriceLabel = $("[for=" + priceTypeUnitPrice.attr("id") + "]");
	priceTypeUnitPriceLabel.text($("#qu_id option:selected").text() + " " + __t("price"));
	refreshPriceHint();
});

function ScanModeSubmit(singleUnit = true)
{
	if (BoolVal(Grocy.UserSettings.scan_mode_purchase_enabled))
	{
		if (singleUnit)
		{
			$("#display_amount").val(1);
			$(".input-group-productamountpicker").trigger("change");
		}

		Grocy.FrontendHelpers.ValidateForm("purchase-form");
		if (Grocy.FrontendHelpers.ValidateForm('purchase-form'))
		{
			$('#save-purchase-button').click();
		}
		else
		{
			toastr.warning(__t("Scan mode is on but not all required fields could be populated automatically"));
			Grocy.UISound.Error();
		}
	}
}
