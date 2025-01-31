var CurrentProductDetails;

$('#save-inventory-button').on('click', function(e)
{
	e.preventDefault();

	if (!Grocy.FrontendHelpers.ValidateForm("inventory-form", true))
	{
		return;
	}

	if ($(".combobox-menu-visible").length)
	{
		return;
	}

	var jsonForm = $('#inventory-form').serializeJSON();
	Grocy.FrontendHelpers.BeginUiBusy("inventory-form");

	Grocy.Api.Get('stock/products/' + jsonForm.product_id,
		function(productDetails)
		{
			var price = "";
			if (jsonForm.price)
			{
				price = Number.parseFloat(jsonForm.price * $("#qu_id option:selected").attr("data-qu-factor")).toFixed(Grocy.UserSettings.stock_decimal_places_prices_input);
			}

			var jsonData = {};
			jsonData.new_amount = jsonForm.amount;
			jsonData.best_before_date = Grocy.Components.DateTimePicker.GetValue();
			jsonData.note = jsonForm.note;
			jsonData.stock_label_type = jsonForm.stock_label_type;
			if (Grocy.FeatureFlags.GROCY_FEATURE_FLAG_STOCK_PRICE_TRACKING)
			{
				jsonData.shopping_location_id = Grocy.Components.ShoppingLocationPicker.GetValue();
			}
			if (Grocy.FeatureFlags.GROCY_FEATURE_FLAG_STOCK_LOCATION_TRACKING)
			{
				jsonData.location_id = Grocy.Components.LocationPicker.GetValue();
			}
			if (Grocy.UserSettings.show_purchased_date_on_purchase)
			{
				jsonData.purchased_date = Grocy.Components.DateTimePicker2.GetValue();
			}

			jsonData.price = price;

			var bookingResponse = null;

			Grocy.Api.Post('stock/products/' + jsonForm.product_id + '/inventory', jsonData,
				function(result)
				{
					bookingResponse = result;

					if (GetUriParam("flow") === "InplaceAddBarcodeToExistingProduct")
					{
						var jsonDataBarcode = {};
						jsonDataBarcode.barcode = GetUriParam("barcode");
						jsonDataBarcode.product_id = jsonForm.product_id;
						jsonDataBarcode.shopping_location_id = jsonForm.shopping_location_id;
						jsonDataBarcode.note = jsonForm.note;

						Grocy.Api.Post('objects/product_barcodes', jsonDataBarcode,
							function(result)
							{
								$("#flow-info-InplaceAddBarcodeToExistingProduct").addClass("d-none");
								$('#barcode-lookup-disabled-hint').addClass('d-none');
								$('#barcode-lookup-hint').removeClass('d-none');
								window.history.replaceState({}, document.title, U("/inventory"));
							},
							function(xhr)
							{
								Grocy.FrontendHelpers.EndUiBusy("inventory-form");
								Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response);
							}
						);
					}

					if (Grocy.FeatureFlags.GROCY_FEATURE_FLAG_LABEL_PRINTER && Number.parseFloat($("#amount").attr("data-estimated-booking-amount")) > 0)
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
					Grocy.Api.Get('stock/products/' + jsonForm.product_id,
						function(result)
						{
							var successMessage = __t('Stock amount of %1$s is now %2$s', result.product.name, result.stock_amount + " " + __n(result.stock_amount, result.quantity_unit_stock.name, result.quantity_unit_stock.name_plural, true)) + '<br><a class="btn btn-secondary btn-sm mt-2" href="#" onclick="UndoStockTransaction(\'' + bookingResponse[0].transaction_id + '\')"><i class="fa-solid fa-undo"></i> ' + __t("Undo") + '</a>';

							if (GetUriParam("embedded") !== undefined)
							{
								Grocy.Components.UserfieldsForm.Save(function()
								{
									window.top.postMessage(WindowMessageBag("BroadcastMessage", WindowMessageBag("ProductChanged", jsonForm.product_id)), Grocy.BaseUrl);
									window.parent.postMessage(WindowMessageBag("ShowSuccessMessage", successMessage), Grocy.BaseUrl);
									window.parent.postMessage(WindowMessageBag("CloseLastModal"), Grocy.BaseUrl);
								});

							}
							else
							{
								Grocy.Components.UserfieldsForm.Save(function()
								{
									Grocy.FrontendHelpers.EndUiBusy("inventory-form");
									toastr.success(successMessage);
									Grocy.Components.ProductPicker.FinishFlow();

									Grocy.Components.ProductAmountPicker.Reset();
									$('#inventory-change-info').addClass('d-none');
									$("#tare-weight-handling-info").addClass("d-none");
									$("#display_amount").attr("min", "0");
									$('#display_amount').val('');
									$('#display_amount').removeAttr("data-not-equal");
									$(".input-group-productamountpicker").trigger("change");
									$('#price').val('');
									$('#note').val('');
									$('#price-hint').text("");
									Grocy.Components.DateTimePicker.Clear();
									Grocy.Components.ProductPicker.SetValue('');
									if (Grocy.FeatureFlags.GROCY_FEATURE_FLAG_STOCK_PRICE_TRACKING)
									{
										Grocy.Components.ShoppingLocationPicker.SetValue('');
									}
									Grocy.Components.ProductPicker.GetInputElement().focus();
									Grocy.Components.ProductCard.Refresh(jsonForm.product_id);
									Grocy.Components.UserfieldsForm.Clear();

									if (Grocy.FeatureFlags.GROCY_FEATURE_FLAG_LABEL_PRINTER)
									{
										$("#stock_label_type").val(0);
									}

									Grocy.FrontendHelpers.ValidateForm('inventory-form');
								});
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
				CurrentProductDetails = productDetails;

				Grocy.Components.ProductAmountPicker.Reload(productDetails.product.id, productDetails.quantity_unit_stock.id);
				Grocy.Components.ProductAmountPicker.SetQuantityUnit(productDetails.quantity_unit_stock.id);

				$('#display_amount').attr("data-stock-amount", productDetails.stock_amount)
				$('#display_amount').attr('data-not-equal', productDetails.stock_amount * $("#qu_id option:selected").attr("data-qu-factor"));

				if (productDetails.product.enable_tare_weight_handling == 1)
				{
					$("#display_amount").attr("min", productDetails.product.tare_weight);
					$("#tare-weight-handling-info").removeClass("d-none");
				}
				else
				{
					$("#display_amount").attr("min", "0");
					$("#tare-weight-handling-info").addClass("d-none");
				}

				if (productDetails.last_price)
				{
					$('#price').val((productDetails.last_price / Number.parseFloat($("#qu_id option:selected").attr("data-qu-factor"))).toFixed(Grocy.UserSettings.stock_decimal_places_prices_display));
				}
				else
				{
					$('#price').val("");
				}

				RefreshLocaleNumberInput();
				if (Grocy.FeatureFlags.GROCY_FEATURE_FLAG_STOCK_PRICE_TRACKING)
				{
					Grocy.Components.ShoppingLocationPicker.SetId(productDetails.last_shopping_location_id);
				}
				if (Grocy.FeatureFlags.GROCY_FEATURE_FLAG_STOCK_LOCATION_TRACKING)
				{
					Grocy.Components.LocationPicker.SetId(productDetails.location.id);
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

				if (Grocy.FeatureFlags.GROCY_FEATURE_FLAG_LABEL_PRINTER)
				{
					$("#stock_label_type").val(productDetails.product.default_stock_label_type);
					$("#stock_label_type").trigger("change");
				}

				if (document.getElementById("product_id").getAttribute("barcode") != "null")
				{
					Grocy.Api.Get('objects/product_barcodes_view?query[]=barcode=' + document.getElementById("product_id").getAttribute("barcode"),
						function(barcodeResult)
						{
							if (barcodeResult)
							{
								var barcode = barcodeResult[0];

								if (barcode)
								{
									if (barcode.amount)
									{
										$("#display_amount").val(barcode.amount);
										$("#display_amount").select();
									}

									if (barcode.qu_id)
									{
										Grocy.Components.ProductAmountPicker.SetQuantityUnit(barcode.qu_id);
									}

									if (barcode.note)
									{
										$("#note").val(barcode.note);
									}

									$(".input-group-productamountpicker").trigger("change");
									Grocy.FrontendHelpers.ValidateForm('inventory-form');
									RefreshLocaleNumberInput();
								}
							}
						},
						function(xhr)
						{
							console.error(xhr);
						}
					);
				}

				$('#display_amount').val(productDetails.stock_amount);
				RefreshLocaleNumberInput();
				$(".input-group-productamountpicker").trigger("change");
				setTimeout(function()
				{
					$('#display_amount').focus();
				}, Grocy.FormFocusDelay);
				$('#display_amount').trigger('keyup');
				RefreshPriceHint();
			},
			function(xhr)
			{
				console.error(xhr);
			}
		);
	}
});

function RefreshPriceHint()
{
	if ($('#amount').val() == 0 || $('#price').val() == 0)
	{
		$('#price-hint').text("");
		return;
	}

	if ($("#qu_id").attr("data-destination-qu-name") != $("#qu_id option:selected").text())
	{
		var amount = $('#display_amount').val();
		if (BoolVal(CurrentProductDetails.product.enable_tare_weight_handling))
		{
			amount -= CurrentProductDetails.product.tare_weight;
		}

		var price = Number.parseFloat($('#price').val() * $("#qu_id option:selected").attr("data-qu-factor")).toFixed(Grocy.UserSettings.stock_decimal_places_prices_display);
		$('#price-hint').text(__t('means %1$s per %2$s', price.toLocaleString(undefined, { style: "currency", currency: Grocy.Currency, minimumFractionDigits: Grocy.UserSettings.stock_decimal_places_prices_display, maximumFractionDigits: Grocy.UserSettings.stock_decimal_places_prices_display }), $("#qu_id").attr("data-destination-qu-name")));
	}
	else
	{
		$('#price-hint').text("");
	}
};

$('#display_amount').val('');
$(".input-group-productamountpicker").trigger("change");
Grocy.FrontendHelpers.ValidateForm('inventory-form');

if (Grocy.Components.ProductPicker.InAnyFlow() === false && GetUriParam("embedded") === undefined)
{
	setTimeout(function()
	{
		Grocy.Components.ProductPicker.GetInputElement().focus();

	}, Grocy.FormFocusDelay);
}
else
{
	Grocy.Components.ProductPicker.GetPicker().trigger('change');

	if (Grocy.Components.ProductPicker.InProductModifyWorkflow())
	{
		setTimeout(function()
		{
			Grocy.Components.ProductPicker.GetInputElement().focus();

		}, Grocy.FormFocusDelay);
	}
}

$('#display_amount').on('focus', function(e)
{
	if (Grocy.Components.ProductPicker.GetValue().length === 0)
	{
		setTimeout(function()
		{
			Grocy.Components.ProductPicker.GetInputElement().focus();

		}, Grocy.FormFocusDelay);
	}
	else
	{
		$(this).select();
	}
});

$('#inventory-form input').keyup(function(event)
{
	Grocy.FrontendHelpers.ValidateForm('inventory-form');
});

$('#inventory-form input').keydown(function(event)
{
	if (event.keyCode === 13) // Enter
	{
		event.preventDefault();

		if (!Grocy.FrontendHelpers.ValidateForm('inventory-form'))
		{
			return false;
		}
		else
		{
			$('#save-inventory-button').click();
		}
	}
});


$('#qu_id').on('change', function(e)
{
	$('#display_amount').attr('data-not-equal', Number.parseFloat($('#display_amount').attr('data-stock-amount')) * Number.parseFloat($("#qu_id option:selected").attr("data-qu-factor")));
	Grocy.FrontendHelpers.ValidateForm('inventory-form');
});

Grocy.Components.DateTimePicker.GetInputElement().on('change', function(e)
{
	Grocy.FrontendHelpers.ValidateForm('inventory-form');
});

Grocy.Components.DateTimePicker.GetInputElement().on('keypress', function(e)
{
	Grocy.FrontendHelpers.ValidateForm('inventory-form');
});

$('#price').on('focus', function(e)
{
	$(this).select();
});

$('#display_amount').on('keyup', function(e)
{
	var productId = Grocy.Components.ProductPicker.GetValue();
	var newAmount = Number.parseFloat($('#amount').val());

	if (productId)
	{
		Grocy.Api.Get('stock/products/' + productId,
			function(productDetails)
			{
				var productStockAmount = productDetails.stock_amount || 0;

				var containerWeight = 0.0;
				if (productDetails.product.enable_tare_weight_handling == 1)
				{
					containerWeight = productDetails.product.tare_weight;
				}

				var estimatedBookingAmount = (newAmount - productStockAmount - containerWeight).toFixed(Grocy.UserSettings.stock_decimal_places_amounts);
				$("#amount").attr("data-estimated-booking-amount", estimatedBookingAmount).trigger("change");
				estimatedBookingAmount = Math.abs(estimatedBookingAmount);
				$('#inventory-change-info').removeClass('d-none');

				if (productDetails.product.enable_tare_weight_handling == 1 && newAmount < containerWeight)
				{
					$('#inventory-change-info').addClass('d-none');
				}
				else if (newAmount > productStockAmount + containerWeight)
				{
					$('#inventory-change-info').text(__t('This means %s will be added to stock', estimatedBookingAmount.toLocaleString() + ' ' + __n(estimatedBookingAmount, productDetails.quantity_unit_stock.name, productDetails.quantity_unit_stock.name_plural, true)));
					Grocy.Components.DateTimePicker.GetInputElement().attr('required', '');
					if (Grocy.FeatureFlags.GROCY_FEATURE_FLAG_STOCK_LOCATION_TRACKING)
					{
						Grocy.Components.LocationPicker.GetInputElement().attr('required', '');
					}
				}
				else if (newAmount < productStockAmount + containerWeight)
				{
					$('#inventory-change-info').text(__t('This means %s will be removed from stock', estimatedBookingAmount.toLocaleString() + ' ' + __n(estimatedBookingAmount, productDetails.quantity_unit_stock.name, productDetails.quantity_unit_stock.name_plural, true)));
					Grocy.Components.DateTimePicker.GetInputElement().removeAttr('required');
					if (Grocy.FeatureFlags.GROCY_FEATURE_FLAG_STOCK_LOCATION_TRACKING)
					{
						Grocy.Components.LocationPicker.GetInputElement().removeAttr('required');
					}
				}
				else if (newAmount == productStockAmount)
				{
					$('#inventory-change-info').addClass('d-none');
				}

				if (!Grocy.FeatureFlags.GROCY_FEATURE_FLAG_STOCK_BEST_BEFORE_DATE_TRACKING)
				{
					Grocy.Components.DateTimePicker.GetInputElement().removeAttr('required');
				}

				RefreshPriceHint();
				Grocy.FrontendHelpers.ValidateForm('inventory-form');
			},
			function(xhr)
			{
				console.error(xhr);
			}
		);
	}
});

$('#qu_id').on('change', function(e)
{
	RefreshPriceHint();
});

function UndoStockBooking(bookingId)
{
	Grocy.Api.Post('stock/bookings/' + bookingId.toString() + '/undo', {},
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
	Grocy.Api.Post('stock/transactions/' + transactionId.toString() + '/undo', {},
		function(result)
		{
			toastr.success(__t("Transaction successfully undone"));
		},
		function(xhr)
		{
			console.error(xhr);
		}
	);
};

$("#display_amount").attr("min", "0");

if (Grocy.FeatureFlags.GROCY_FEATURE_FLAG_LABEL_PRINTER)
{
	$("#stock_label_type, #amount").on("change", function(e)
	{
		if ($("#stock_label_type").val() == 2)
		{
			var estimatedBookingAmount = Number.parseFloat($("#amount").attr("data-estimated-booking-amount"));
			if (estimatedBookingAmount > 0)
			{
				$("#stock-entry-label-info").text(__n(estimatedBookingAmount, "This means 1 label will be printed", "This means %1$s labels will be printed"));
			}
			else
			{
				$("#stock-entry-label-info").text("");
			}
		}
		else
		{
			$("#stock-entry-label-info").text("");
		}
	});
}

Grocy.Components.UserfieldsForm.Load();
