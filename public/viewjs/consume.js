$('#save-consume-button').on('click', function(e)
{
	e.preventDefault();

	var jsonForm = $('#consume-form').serializeJSON();
	Grocy.FrontendHelpers.BeginUiBusy("consume-form");

	var apiUrl = 'stock/products/' + jsonForm.product_id + '/consume';

	var jsonData = {};
	jsonData.amount = jsonForm.amount;
	jsonData.spoiled = $('#spoiled').is(':checked');

	if ($("#use_specific_stock_entry").is(":checked"))
	{
		jsonData.stock_entry_id = jsonForm.specific_stock_entry;
	}

	if (Grocy.FeatureFlags.GROCY_FEATURE_FLAG_STOCK_LOCATION_TRACKING)
	{
		jsonData.location_id = $("#location_id").val();
	}

	if (Grocy.FeatureFlags.GROCY_FEATURE_FLAG_RECIPES && Grocy.Components.RecipePicker.GetValue().toString().length > 0)
	{
		jsonData.recipe_id = Grocy.Components.RecipePicker.GetValue();
	}

	var bookingResponse = null;

	Grocy.Api.Get('stock/products/' + jsonForm.product_id,
		function(productDetails)
		{
			Grocy.Api.Post(apiUrl, jsonData,
				function(result)
				{
					if (BoolVal(Grocy.UserSettings.scan_mode_consume_enabled))
					{
						Grocy.UISound.Success();
					}

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
								window.history.replaceState({ }, document.title, U("/consume"));
							},
							function(xhr)
							{
								console.error(xhr);
							}
						);
					}

					$("#specific_stock_entry").find("option").remove().end().append("<option></option>");
					if ($("#use_specific_stock_entry").is(":checked"))
					{
						$("#use_specific_stock_entry").click();
					}

					if (productDetails.product.enable_tare_weight_handling == 1)
					{
						var successMessage = __t('Removed %1$s of %2$s from stock', Math.abs(jsonForm.amount - parseFloat(productDetails.product.tare_weight)) + " " + __n(jsonForm.amount, productDetails.quantity_unit_stock.name, productDetails.quantity_unit_stock.name_plural), productDetails.product.name) + '<br><a class="btn btn-secondary btn-sm mt-2" href="#" onclick="UndoStockTransaction(\'' + bookingResponse.transaction_id + '\')"><i class="fas fa-undo"></i> ' + __t("Undo") + '</a>';
					}
					else
					{
						var successMessage = __t('Removed %1$s of %2$s from stock', Math.abs(jsonForm.amount) + " " + __n(jsonForm.amount, productDetails.quantity_unit_stock.name, productDetails.quantity_unit_stock.name_plural), productDetails.product.name) + '<br><a class="btn btn-secondary btn-sm mt-2" href="#" onclick="UndoStockTransaction(\'' + bookingResponse.transaction_id + '\')"><i class="fas fa-undo"></i> ' + __t("Undo") + '</a>';
					}

					if (GetUriParam("embedded") !== undefined)
					{
						window.parent.postMessage(WindowMessageBag("ProductChanged", jsonForm.product_id), Grocy.BaseUrl);
						window.parent.postMessage(WindowMessageBag("ShowSuccessMessage", successMessage), Grocy.BaseUrl);
						window.parent.postMessage(WindowMessageBag("CloseAllModals"), Grocy.BaseUrl);
					}
					else
					{
						Grocy.FrontendHelpers.EndUiBusy("consume-form");
						toastr.success(successMessage);

						$("#amount").attr("min", "1");
						$("#amount").attr("max", "999999");
						$("#amount").attr("step", "1");
						$("#amount").parent().find(".invalid-feedback").text(__t('The amount cannot be lower than %s', '1'));
						$('#amount').val(parseFloat(Grocy.UserSettings.stock_default_consume_amount).toLocaleString({ minimumFractionDigits: 0, maximumFractionDigits: 4 }));
						$('#amount_qu_unit').text("");
						$("#tare-weight-handling-info").addClass("d-none");
						Grocy.Components.ProductPicker.Clear();
						if (Grocy.FeatureFlags.GROCY_FEATURE_FLAG_RECIPES)
						{
							Grocy.Components.RecipePicker.Clear();
						}
						if (Grocy.FeatureFlags.GROCY_FEATURE_FLAG_STOCK_LOCATION_TRACKING)
						{
							$("#location_id").find("option").remove().end().append("<option></option>");
						}
						Grocy.Components.ProductPicker.GetInputElement().focus();
						Grocy.Components.ProductCard.Refresh(jsonForm.product_id);
						Grocy.FrontendHelpers.ValidateForm('consume-form');
					}
				},
				function(xhr)
				{
					Grocy.FrontendHelpers.EndUiBusy("consume-form");
					console.error(xhr);
				}
			);
		},
		function(xhr)
		{
			Grocy.FrontendHelpers.EndUiBusy("consume-form");
			console.error(xhr);
		}
	);
});

$('#save-mark-as-open-button').on('click', function(e)
{
	e.preventDefault();

	var jsonForm = $('#consume-form').serializeJSON();
	Grocy.FrontendHelpers.BeginUiBusy("consume-form");

	var apiUrl = 'stock/products/' + jsonForm.product_id + '/open';

	jsonData = { };
	jsonData.amount = jsonForm.amount;

	if ($("#use_specific_stock_entry").is(":checked"))
	{
		jsonData.stock_entry_id = jsonForm.specific_stock_entry;
	}

	Grocy.Api.Get('stock/products/' + jsonForm.product_id,
		function(productDetails)
		{
			Grocy.Api.Post(apiUrl, jsonData,
				function(result)
				{
					$("#specific_stock_entry").find("option").remove().end().append("<option></option>");
					if ($("#use_specific_stock_entry").is(":checked"))
					{
						$("#use_specific_stock_entry").click();
					}

					Grocy.FrontendHelpers.EndUiBusy("consume-form");
					toastr.success(__t('Marked %1$s of %2$s as opened', jsonForm.amount + " " + __n(jsonForm.amount, productDetails.quantity_unit_stock.name, productDetails.quantity_unit_stock.name_plural), productDetails.product.name) + '<br><a class="btn btn-secondary btn-sm mt-2" href="#" onclick="UndoStockTransaction(\'' + result.transaction_id + '\')"><i class="fas fa-undo"></i> ' + __t("Undo") + '</a>');

					$('#amount').val(parseFloat(Grocy.UserSettings.stock_default_consume_amount).toLocaleString({ minimumFractionDigits: 0, maximumFractionDigits: 4 }));
					Grocy.Components.ProductPicker.Clear();
					Grocy.Components.ProductPicker.GetInputElement().focus();
					Grocy.FrontendHelpers.ValidateForm('consume-form');
				},
				function(xhr)
				{
					Grocy.FrontendHelpers.EndUiBusy("consume-form");
					console.error(xhr);
				}
			);
		},
		function(xhr)
		{
			Grocy.FrontendHelpers.EndUiBusy("consume-form");
			console.error(xhr);
		}
	);
});

$("#location_id").on('change', function(e)
{
	var locationId = $(e.target).val();
	var sumValue = 0;
	var stockId = null;

	$("#specific_stock_entry").find("option").remove().end().append("<option></option>");
	if ($("#use_specific_stock_entry").is(":checked"))
	{
			$("#use_specific_stock_entry").click();
	}

	if (GetUriParam("embedded") !== undefined)
	{
		 stockId = GetUriParam('stockId');
	}

	if (locationId)
	{
		Grocy.Api.Get("stock/products/" + Grocy.Components.ProductPicker.GetValue() + '/entries',
			function(stockEntries)
			{
				stockEntries.forEach(stockEntry =>
				{
					var openTxt = __t("Not opened");
					if (stockEntry.open == 1)
					{
						openTxt = __t("Opened");
					}

					if (stockEntry.location_id == locationId)
					{
						$("#specific_stock_entry").append($("<option>", {
							value: stockEntry.stock_id,
							amount: stockEntry.amount,
							text: __t("Amount: %1$s; Expires on %2$s; Bought on %3$s", stockEntry.amount, moment(stockEntry.best_before_date).format("YYYY-MM-DD"), moment(stockEntry.purchased_date).format("YYYY-MM-DD")) + "; " + openTxt
						}));
						
						sumValue = sumValue + parseFloat(stockEntry.amount);

						if (stockEntry.stock_id == stockId)
						{
							$("#specific_stock_entry").val(stockId);
						}
					}
				});

				Grocy.Api.Get('stock/products/' + Grocy.Components.ProductPicker.GetValue(),
					function(productDetails)
					{
						if (productDetails.product.enable_tare_weight_handling == 1)
						{
							$("#amount").attr("min", productDetails.product.tare_weight);
							$('#amount').attr('max', sumValue + parseFloat(productDetails.product.tare_weight));
							$("#amount").parent().find(".invalid-feedback").text(__t('The amount must be between %1$s and %2$s', parseFloat(productDetails.product.tare_weight).toLocaleString(), (parseFloat(productDetails.stock_amount) + parseFloat(productDetails.product.tare_weight)).toLocaleString()));
							$("#tare-weight-handling-info").removeClass("d-none");
						}
						else
						{
							$("#tare-weight-handling-info").addClass("d-none");

							if (productDetails.product.allow_partial_units_in_stock == 1)
							{
								$("#amount").attr("min", "0.01");
								$("#amount").attr("step", "0.01");
								$("#amount").parent().find(".invalid-feedback").text(__t('The amount must be between %1$s and %2$s', 0.01.toLocaleString(), parseFloat(productDetails.stock_amount).toLocaleString()));
							}
							else
							{
								$("#amount").attr("min", "1");
								$("#amount").attr("step", "1");
								$("#amount").parent().find(".invalid-feedback").text(__t('The amount must be between %1$s and %2$s', "1", parseFloat(productDetails.stock_amount).toLocaleString()));
							}

							$('#amount').attr('max', sumValue);

							if (sumValue == 0)
							{
								$("#amount").parent().find(".invalid-feedback").text(__t('There are no units available at this location'));
							}
						}
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
	}
});

Grocy.Components.ProductPicker.GetPicker().on('change', function(e)
{
	if (BoolVal(Grocy.UserSettings.scan_mode_consume_enabled))
	{
		Grocy.UISound.BarcodeScannerBeep();
	}

	$("#specific_stock_entry").find("option").remove().end().append("<option></option>");
	if ($("#use_specific_stock_entry").is(":checked"))
	{
		$("#use_specific_stock_entry").click();
	}
	$("#location_id").val("");

	var productId = $(e.target).val();

	if (productId)
	{
		Grocy.Components.ProductCard.Refresh(productId);

		Grocy.Api.Get('stock/products/' + productId,
			function(productDetails)
			{
				$('#amount_qu_unit').text(productDetails.quantity_unit_stock.name);

				$("#location_id").find("option").remove().end().append("<option></option>");
				Grocy.Api.Get("stock/products/" + productId + '/locations',
					function(stockLocations)
					{
						var setDefault = 0;
						stockLocations.forEach(stockLocation =>
						{
							if (productDetails.location.id == stockLocation.location_id)
							{
								$("#location_id").append($("<option>", {
									value: stockLocation.location_id,
									text: stockLocation.location_name + " (" + __t("Default location") + ")"
								}));
								$("#location_id").val(productDetails.location.id);
								$("#location_id").trigger('change');
								setDefault = 1;
							}
							else
							{
								$("#location_id").append($("<option>", {
									value: stockLocation.location_id,
									text: stockLocation.location_name
								}));
							}

							if (setDefault == 0)
							{
								$("#location_id").val(stockLocation.location_id);
								$("#location_id").trigger('change');
							}
						});

						if (BoolVal(Grocy.UserSettings.scan_mode_consume_enabled))
						{
							$("#amount").val(1);
							Grocy.FrontendHelpers.ValidateForm("consume-form");
							if (document.getElementById("consume-form").checkValidity() === true)
							{
								$('#save-consume-button').click();
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

				if (productDetails.product.allow_partial_units_in_stock == 1)
				{
					$("#amount").attr("min", "0.01");
					$("#amount").attr("step", "0.01");
					$("#amount").parent().find(".invalid-feedback").text(__t('The amount must be between %1$s and %2$s', 0.01.toLocaleString(), parseFloat(productDetails.stock_amount).toLocaleString()));
				}
				else
				{
					$("#amount").attr("min", "1");
					$("#amount").attr("step", "1");
					$("#amount").parent().find(".invalid-feedback").text(__t('The amount must be between %1$s and %2$s', "1", parseFloat(productDetails.stock_amount).toLocaleString()));
				}

				if (productDetails.product.enable_tare_weight_handling == 1)
				{
					$("#amount").attr("min", productDetails.product.tare_weight);
					$('#amount').attr('max', parseFloat(productDetails.stock_amount) + parseFloat(productDetails.product.tare_weight));
					$("#amount").parent().find(".invalid-feedback").text(__t('The amount must be between %1$s and %2$s', parseFloat(productDetails.product.tare_weight).toLocaleString(), (parseFloat(productDetails.stock_amount) + parseFloat(productDetails.product.tare_weight)).toLocaleString()));
					$("#tare-weight-handling-info").removeClass("d-none");
				}
				else
				{
					$("#tare-weight-handling-info").addClass("d-none");
				}

				if ((parseFloat(productDetails.stock_amount) || 0) === 0)
				{
					Grocy.Components.ProductPicker.Clear();
					Grocy.FrontendHelpers.ValidateForm('consume-form');
					Grocy.Components.ProductPicker.ShowCustomError(__t('This product is not in stock'));
					Grocy.Components.ProductPicker.GetInputElement().focus();
				}
				else
				{
					Grocy.Components.ProductPicker.HideCustomError();
					Grocy.FrontendHelpers.ValidateForm('consume-form');
					$('#amount').focus();
				}

				if (productDetails.stock_amount == productDetails.stock_amount_opened)
				{
					$("#save-mark-as-open-button").addClass("disabled");
				}
				else
				{
					$("#save-mark-as-open-button").removeClass("disabled");
				}
			},
			function(xhr)
			{
				console.error(xhr);
			}
		);
	}
});

$('#amount').val(parseFloat(Grocy.UserSettings.stock_default_consume_amount).toLocaleString({ minimumFractionDigits: 0, maximumFractionDigits: 4 }));
Grocy.FrontendHelpers.ValidateForm('consume-form');

$('#amount').on('focus', function(e)
{
	$(this).select();
});

$('#consume-form input').keyup(function(event)
{
	Grocy.FrontendHelpers.ValidateForm('consume-form');
});

$('#consume-form select').change(function(event)
{
	Grocy.FrontendHelpers.ValidateForm('consume-form');
});

$('#consume-form input').keydown(function(event)
{
	if (event.keyCode === 13) //Enter
	{
		event.preventDefault();

		if (document.getElementById('consume-form').checkValidity() === false) //There is at least one validation error
		{
			return false;
		}
		else
		{
			$('#save-consume-button').click();
		}
	}
});

$("#specific_stock_entry").on("change", function(e)
{
	if ($(e.target).val() == "")
	{
	var sumValue = 0;
		Grocy.Api.Get("stock/products/" + Grocy.Components.ProductPicker.GetValue() + '/entries',
			function(stockEntries)
			{
				stockEntries.forEach(stockEntry =>
				{
					if (stockEntry.location_id == $("#location_id").val() || stockEntry.location_id == "")
					{
						sumValue = sumValue + parseFloat(stockEntry.amount);
					}
				});
				$("#amount").attr("max", sumValue);
				if (sumValue == 0)
				{
					$("#amount").parent().find(".invalid-feedback").text(__t('There are no units available at this location'));
				}
				else
				{
					$("#amount").parent().find(".invalid-feedback").text(__t('The amount must be between %1$s and %2$s', "1", sumValue));
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
		$("#amount").parent().find(".invalid-feedback").text(__t('The amount must be between %1$s and %2$s', "1", $('option:selected', this).attr('amount')));
		$("#amount").attr("max", $('option:selected', this).attr('amount'));
	}
});

$("#use_specific_stock_entry").on("change", function()
{
	var value = $(this).is(":checked");

	if (value)
	{
		$("#specific_stock_entry").removeAttr("disabled");
		$("#specific_stock_entry").attr("required", "");
	}
	else
	{
		$("#specific_stock_entry").attr("disabled", "");
		$("#specific_stock_entry").removeAttr("required");
		$("#specific_stock_entry").val("");
		$("#location_id").trigger('change');
	}

	Grocy.FrontendHelpers.ValidateForm("consume-form");
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

if (GetUriParam("embedded") !== undefined)
{
	var locationId = GetUriParam('locationId');

	if (typeof locationId === 'undefined')
	{
		Grocy.Components.ProductPicker.GetPicker().trigger('change');
		Grocy.Components.ProductPicker.GetInputElement().focus();
	}
	else
	{
		$("#location_id").val(locationId);
		$("#location_id").trigger('change');
		$("#use_specific_stock_entry").click();
		$("#use_specific_stock_entry").trigger('change');
	}
}

// Default input field
Grocy.Components.ProductPicker.GetInputElement().focus();

$(document).on("change", "#scan-mode", function(e)
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
