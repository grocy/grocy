$('#save-consume-button').on('click', function(e)
{
	e.preventDefault();

	if ($(".combobox-menu-visible").length)
	{
		return;
	}

	var jsonForm = $('#consume-form').serializeJSON();
	Grocy.FrontendHelpers.BeginUiBusy("consume-form");

	var apiUrl = 'stock/products/' + jsonForm.product_id + '/consume';

	var jsonData = {};
	jsonData.amount = jsonForm.amount;
	jsonData.exact_amount = $('#consume-exact-amount').is(':checked');
	jsonData.spoiled = $('#spoiled').is(':checked');
	jsonData.allow_subproduct_substitution = true;

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

					if (GetUriParam("flow") === "InplaceAddBarcodeToExistingProduct")
					{
						var jsonDataBarcode = {};
						jsonDataBarcode.barcode = GetUriParam("barcode");
						jsonDataBarcode.product_id = jsonForm.product_id;

						Grocy.Api.Post('objects/product_barcodes', jsonDataBarcode,
							function(result)
							{
								$("#flow-info-InplaceAddBarcodeToExistingProduct").addClass("d-none");
								$('#barcode-lookup-disabled-hint').addClass('d-none');
								$('#barcode-lookup-hint').removeClass('d-none');
								window.history.replaceState({}, document.title, U("/consume"));
							},
							function(xhr)
							{
								Grocy.FrontendHelpers.EndUiBusy("consume-form");
								Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response);
							}
						);
					}

					$("#specific_stock_entry").find("option").remove().end().append("<option></option>");
					if ($("#use_specific_stock_entry").is(":checked"))
					{
						$("#use_specific_stock_entry").click();
					}

					if (productDetails.product.enable_tare_weight_handling == 1 && !jsonData.exact_amount)
					{
						var successMessage = __t('Removed %1$s of %2$s from stock', Math.abs(jsonForm.amount - (parseFloat(productDetails.product.tare_weight) + parseFloat(productDetails.stock_amount))) + " " + __n(jsonForm.amount, productDetails.quantity_unit_stock.name, productDetails.quantity_unit_stock.name_plural), productDetails.product.name) + '<br><a class="btn btn-secondary btn-sm mt-2" href="#" onclick="UndoStockTransaction(\'' + bookingResponse[0].transaction_id + '\')"><i class="fas fa-undo"></i> ' + __t("Undo") + '</a>';
					}
					else
					{
						var successMessage = __t('Removed %1$s of %2$s from stock', Math.abs(jsonForm.amount) + " " + __n(jsonForm.amount, productDetails.quantity_unit_stock.name, productDetails.quantity_unit_stock.name_plural), productDetails.product.name) + '<br><a class="btn btn-secondary btn-sm mt-2" href="#" onclick="UndoStockTransaction(\'' + bookingResponse[0].transaction_id + '\')"><i class="fas fa-undo"></i> ' + __t("Undo") + '</a>';
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
						Grocy.Components.ProductPicker.FinishFlow();

						Grocy.Components.ProductAmountPicker.Reset();
						$("#display_amount").attr("min", Grocy.DefaultMinAmount);
						$("#display_amount").removeAttr("max");
						if (BoolVal(Grocy.UserSettings.stock_default_consume_amount_use_quick_consume_amount))
						{
							$('#display_amount').val(productDetails.product.quick_consume_amount);
						}
						else
						{
							$('#display_amount').val(parseFloat(Grocy.UserSettings.stock_default_consume_amount));
						}
						RefreshLocaleNumberInput();
						$(".input-group-productamountpicker").trigger("change");
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
						$("#consume-exact-amount-group").addClass("d-none");
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

	if ($(".combobox-menu-visible").length)
	{
		return;
	}

	var jsonForm = $('#consume-form').serializeJSON();
	Grocy.FrontendHelpers.BeginUiBusy("consume-form");

	var apiUrl = 'stock/products/' + jsonForm.product_id + '/open';

	jsonData = {};
	jsonData.amount = jsonForm.amount;
	jsonData.allow_subproduct_substitution = true;

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
					toastr.success(__t('Marked %1$s of %2$s as opened', parseFloat(jsonForm.amount).toLocaleString({ minimumFractionDigits: 0, maximumFractionDigits: Grocy.UserSettings.stock_decimal_places_amounts }) + " " + __n(jsonForm.amount, productDetails.quantity_unit_stock.name, productDetails.quantity_unit_stock.name_plural), productDetails.product.name) + '<br><a class="btn btn-secondary btn-sm mt-2" href="#" onclick="UndoStockTransaction(\'' + result[0].transaction_id + '\')"><i class="fas fa-undo"></i> ' + __t("Undo") + '</a>');

					if (BoolVal(Grocy.UserSettings.stock_default_consume_amount_use_quick_consume_amount))
					{
						$('#display_amount').val(productDetails.product.quick_consume_amount);
					}
					else
					{
						$('#display_amount').val(parseFloat(Grocy.UserSettings.stock_default_consume_amount));
					}
					RefreshLocaleNumberInput();
					$(".input-group-productamountpicker").trigger("change");
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
var sumValue = 0;
$("#location_id").on('change', function(e)
{
	var locationId = $(e.target).val();
	sumValue = 0;
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
		Grocy.Api.Get("stock/products/" + Grocy.Components.ProductPicker.GetValue() + '/entries?include_sub_products=true',
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
							text: __t("Amount: %1$s; Due on %2$s; Bought on %3$s", stockEntry.amount, moment(stockEntry.best_before_date).format("YYYY-MM-DD"), moment(stockEntry.purchased_date).format("YYYY-MM-DD")) + "; " + openTxt
						}));

						sumValue = sumValue + parseFloat(stockEntry.amount || 0);

						if (stockEntry.stock_id == stockId)
						{
							$("#specific_stock_entry").val(stockId);
						}
					}
				});

				Grocy.Api.Get('stock/products/' + Grocy.Components.ProductPicker.GetValue(),
					function(productDetails)
					{
						current_productDetails = productDetails;
						RefreshForm();
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
				current_productDetails = productDetails;

				Grocy.Components.ProductAmountPicker.Reload(productDetails.product.id, productDetails.quantity_unit_stock.id);
				Grocy.Components.ProductAmountPicker.SetQuantityUnit(productDetails.quantity_unit_stock.id);
				if (BoolVal(Grocy.UserSettings.stock_default_consume_amount_use_quick_consume_amount))
				{
					$('#display_amount').val(productDetails.product.quick_consume_amount);
				}
				else
				{
					$('#display_amount').val(parseFloat(Grocy.UserSettings.stock_default_consume_amount));
				}
				RefreshLocaleNumberInput();
				$(".input-group-productamountpicker").trigger("change");

				$("#location_id").find("option").remove().end().append("<option></option>");
				Grocy.Api.Get("stock/products/" + productId + '/locations?include_sub_products=true',
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
							$("#display_amount").val(1);
							RefreshLocaleNumberInput();
							$(".input-group-productamountpicker").trigger("change");

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

				if (productDetails.product.enable_tare_weight_handling == 1)
				{
					$("#display_amount").attr("min", productDetails.product.tare_weight);
					$('#display_amount').attr('max', parseFloat(productDetails.stock_amount) + parseFloat(productDetails.product.tare_weight));
					$("#tare-weight-handling-info").removeClass("d-none");
				}
				else
				{
					$("#display_amount").attr("min", Grocy.DefaultMinAmount);
					$("#tare-weight-handling-info").addClass("d-none");
				}

				if ((parseFloat(productDetails.stock_amount_aggregated) || 0) === 0)
				{
					Grocy.Components.ProductAmountPicker.Reset();
					Grocy.Components.ProductPicker.Clear();
					Grocy.FrontendHelpers.ValidateForm('consume-form');
					Grocy.Components.ProductPicker.ShowCustomError(__t('This product is not in stock'));
					Grocy.Components.ProductPicker.GetInputElement().focus();
				}
				else
				{
					Grocy.Components.ProductPicker.HideCustomError();
					Grocy.FrontendHelpers.ValidateForm('consume-form');
					$('#display_amount').focus();
				}

				if (productDetails.stock_amount == productDetails.stock_amount_opened || productDetails.product.enable_tare_weight_handling == 1)
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

$('#display_amount').val(parseFloat(Grocy.UserSettings.stock_default_consume_amount));
$(".input-group-productamountpicker").trigger("change");
Grocy.FrontendHelpers.ValidateForm('consume-form');

$('#display_amount').on('focus', function(e)
{
	$(this).select();
});

$('#price').on('focus', function(e)
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
		sumValue = 0;
		Grocy.Api.Get("stock/products/" + Grocy.Components.ProductPicker.GetValue() + '/entries?include_sub_products=true',
			function(stockEntries)
			{
				stockEntries.forEach(stockEntry =>
				{
					if (stockEntry.location_id == $("#location_id").val() || stockEntry.location_id == "")
					{
						sumValue = sumValue + parseFloat(stockEntry.amount_aggregated);
					}
				});
				$("#display_amount").attr("max", sumValue);
				if (sumValue == 0)
				{
					$("#display_amount").parent().find(".invalid-feedback").text(__t('There are no units available at this location'));
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
		$("#display_amount").attr("max", $('option:selected', this).attr('amount'));
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

$("#qu_id").on("change", function()
{
	RefreshForm();
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

$('#consume-exact-amount').on('change', RefreshForm);
var current_productDetails;
function RefreshForm()
{
	var productDetails = current_productDetails;
	if (productDetails.product.enable_tare_weight_handling == 1)
	{
		$("#consume-exact-amount-group").removeClass("d-none");
	}
	else
	{
		$("#consume-exact-amount-group").addClass("d-none");
	}

	if (productDetails.product.enable_tare_weight_handling == 1 && !$('#consume-exact-amount').is(':checked'))
	{
		$("#display_amount").attr("min", productDetails.product.tare_weight);
		$('#display_amount').attr('max', sumValue + parseFloat(productDetails.product.tare_weight));
		$("#tare-weight-handling-info").removeClass("d-none");
	}
	else
	{
		$("#tare-weight-handling-info").addClass("d-none");

		$("#display_amount").attr("min", Grocy.DefaultMinAmount);
		$('#display_amount').attr('max', sumValue * $("#qu_id option:selected").attr("data-qu-factor"));

		if (sumValue == 0)
		{
			$("#display_amount").parent().find(".invalid-feedback").text(__t('There are no units available at this location'));
		}
	}

	Grocy.FrontendHelpers.ValidateForm("consume-form");
}
