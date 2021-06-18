﻿$('#save-transfer-button').on('click', function(e)
{
	e.preventDefault();

	if ($(".combobox-menu-visible").length)
	{
		return;
	}

	var jsonForm = $('#transfer-form').serializeJSON();
	Grocy.FrontendHelpers.BeginUiBusy("transfer-form");

	var apiUrl = 'stock/products/' + jsonForm.product_id + '/transfer';

	var jsonData = {};
	jsonData.amount = jsonForm.amount;
	jsonData.location_id_to = $("#location_id_to").val();
	jsonData.location_id_from = $("#location_id_from").val();

	if ($("#use_specific_stock_entry").is(":checked"))
	{
		jsonData.stock_entry_id = jsonForm.specific_stock_entry;
	}

	var bookingResponse = null;

	Grocy.Api.Get('stock/products/' + jsonForm.product_id,
		function(productDetails)
		{
			Grocy.Api.Post(apiUrl, jsonData,
				function(result)
				{
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
								window.history.replaceState({}, document.title, U("/transfer"));
							},
							function(xhr)
							{
								Grocy.FrontendHelpers.EndUiBusy("transfer-form");
								Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response);
							}
						);
					}

					if (productDetails.product.enable_tare_weight_handling == 1)
					{
						var successMessage = __t('Transfered %1$s of %2$s from %3$s to %4$s', Math.abs(jsonForm.amount - parseFloat(productDetails.product.tare_weight)) + " " + __n(jsonForm.amount, productDetails.quantity_unit_stock.name, productDetails.quantity_unit_stock.name_plural), productDetails.product.name, $('option:selected', "#location_id_from").text(), $('option:selected', "#location_id_to").text()) + '<br><a class="btn btn-secondary btn-sm mt-2" href="#" onclick="UndoStockTransaction(\'' + bookingResponse[0].transaction_id + '\')"><i class="fas fa-undo"></i> ' + __t("Undo") + '</a>';
					}
					else
					{
						var successMessage = __t('Transfered %1$s of %2$s from %3$s to %4$s', Math.abs(jsonForm.amount) + " " + __n(jsonForm.amount, productDetails.quantity_unit_stock.name, productDetails.quantity_unit_stock.name_plural), productDetails.product.name, $('option:selected', "#location_id_from").text(), $('option:selected', "#location_id_to").text()) + '<br><a class="btn btn-secondary btn-sm mt-2" href="#" onclick="UndoStockTransaction(\'' + bookingResponse[0].transaction_id + '\')"><i class="fas fa-undo"></i> ' + __t("Undo") + '</a>';
					}

					if (GetUriParam("embedded") !== undefined)
					{
						window.parent.postMessage(WindowMessageBag("ProductChanged", jsonForm.product_id), Grocy.BaseUrl);
						window.parent.postMessage(WindowMessageBag("ShowSuccessMessage", successMessage), Grocy.BaseUrl);
						window.parent.postMessage(WindowMessageBag("CloseAllModals"), Grocy.BaseUrl);
					}
					else
					{
						Grocy.FrontendHelpers.EndUiBusy("transfer-form");
						toastr.success(successMessage);
						Grocy.Components.ProductPicker.FinishFlow();

						if (parseInt($("#location_id_from option:selected").attr("data-is-freezer")) === 0 && parseInt($("#location_id_to option:selected").attr("data-is-freezer")) === 1) // Frozen
						{
							toastr.info('<span>' + __t("Frozen") + "</span> <i class='fas fa-snowflake'></i>");
						}
						if (parseInt($("#location_id_from option:selected").attr("data-is-freezer")) === 1 && parseInt($("#location_id_to option:selected").attr("data-is-freezer")) === 0) // Thawed
						{
							toastr.info('<span>' + __t("Thawed") + "</span> <i class='fas fa-fire-alt'></i>");
						}

						$("#specific_stock_entry").find("option").remove().end().append("<option></option>");
						$("#specific_stock_entry").attr("disabled", "");
						$("#specific_stock_entry").removeAttr("required");
						if ($("#use_specific_stock_entry").is(":checked"))
						{
							$("#use_specific_stock_entry").click();
						}

						Grocy.Components.ProductAmountPicker.Reset();
						$("#location_id_from").find("option").remove().end().append("<option></option>");
						$("#display_amount").attr("min", Grocy.DefaultMinAmount);
						$("#display_amount").removeAttr("max");
						$('#display_amount').val(parseFloat(Grocy.UserSettings.stock_default_transfer_amount));
						RefreshLocaleNumberInput();
						$(".input-group-productamountpicker").trigger("change");
						$("#tare-weight-handling-info").addClass("d-none");
						Grocy.Components.ProductPicker.Clear();
						$("#location_id_to").val("");
						$("#location_id_from").val("");
						Grocy.Components.ProductPicker.GetInputElement().focus();
						Grocy.Components.ProductCard.Refresh(jsonForm.product_id);
						Grocy.FrontendHelpers.ValidateForm('transfer-form');
					}
				},
				function(xhr)
				{
					Grocy.FrontendHelpers.EndUiBusy("transfer-form");
					console.error(xhr);
				}
			);
		},
		function(xhr)
		{
			Grocy.FrontendHelpers.EndUiBusy("transfer-form");
			console.error(xhr);
		}
	);
});

Grocy.Components.ProductPicker.GetPicker().on('change', function(e)
{
	$("#specific_stock_entry").find("option").remove().end().append("<option></option>");
	if ($("#use_specific_stock_entry").is(":checked") && GetUriParam("stockId") == null)
	{
		$("#use_specific_stock_entry").click();
	}
	$("#location_id_to").val("");
	if (GetUriParam("stockId") == null)
	{
		$("#location_id_from").val("");
	}

	var productId = $(e.target).val();

	if (productId)
	{
		Grocy.Components.ProductCard.Refresh(productId);

		Grocy.Api.Get('stock/products/' + productId,
			function(productDetails)
			{
				Grocy.Components.ProductAmountPicker.Reload(productDetails.product.id, productDetails.quantity_unit_stock.id);
				Grocy.Components.ProductAmountPicker.SetQuantityUnit(productDetails.quantity_unit_stock.id);

				if (productDetails.product.enable_tare_weight_handling == 1)
				{
					Grocy.Components.ProductPicker.GetPicker().parent().find(".invalid-feedback").text(__t('Products with tare weight enabled are currently not supported for transfer'));
					Grocy.Components.ProductPicker.Clear();
					return;
				}

				$("#location_id_from").find("option").remove().end().append("<option></option>");
				Grocy.Api.Get("stock/products/" + productId + '/locations',
					function(stockLocations)
					{
						var setDefault = 0;
						stockLocations.forEach(stockLocation =>
						{
							if (productDetails.location.id == stockLocation.location_id)
							{
								$("#location_id_from").append($("<option>", {
									value: stockLocation.location_id,
									text: stockLocation.location_name + " (" + __t("Default location") + ")",
									"data-is-freezer": stockLocation.location_is_freezer
								}));
								$("#location_id_from").val(productDetails.location.id);
								$("#location_id_from").trigger('change');
								setDefault = 1;
							}
							else
							{
								$("#location_id_from").append($("<option>", {
									value: stockLocation.location_id,
									text: stockLocation.location_name,
									"data-is-freezer": stockLocation.location_is_freezer
								}));
							}

							if (setDefault == 0)
							{
								$("#location_id_from").val(stockLocation.location_id);
								$("#location_id_from").trigger('change');
							}
						});

						if (GetUriParam("locationId") != null)
						{
							$("#location_id_from").val(GetUriParam("locationId"));
							$("#location_id_from").trigger("change");
						}
					},
					function(xhr)
					{
						console.error(xhr);
					}
				);

				if (document.getElementById("product_id").getAttribute("barcode") != "null")
				{
					Grocy.Api.Get('objects/product_barcodes?query[]=barcode=' + document.getElementById("product_id").getAttribute("barcode"),
						function(barcodeResult)
						{
							if (barcodeResult != null)
							{
								var barcode = barcodeResult[0];

								if (barcode != null)
								{
									if (barcode.amount != null && !barcode.amount.isEmpty())
									{
										$("#display_amount").val(barcode.amount);
										$("#display_amount").select();
									}

									if (barcode.qu_id != null)
									{
										Grocy.Components.ProductAmountPicker.SetQuantityUnit(barcode.qu_id);
									}

									$(".input-group-productamountpicker").trigger("change");
									Grocy.FrontendHelpers.ValidateForm('transfer-form');
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

				if (productDetails.product.enable_tare_weight_handling == 1)
				{
					$("#display_amount").attr("min", productDetails.product.tare_weight);
					$("#tare-weight-handling-info").removeClass("d-none");
				}
				else
				{
					$("#display_amount").attr("min", Grocy.DefaultMinAmount);
					$("#tare-weight-handling-info").addClass("d-none");
				}

				$('#display_amount').attr("data-stock-amount", productDetails.stock_amount);

				if ((parseFloat(productDetails.stock_amount) || 0) === 0)
				{
					Grocy.Components.ProductPicker.Clear();
					Grocy.FrontendHelpers.ValidateForm('transfer-form');
					Grocy.Components.ProductPicker.ShowCustomError(__t('This product is not in stock'));
					Grocy.Components.ProductPicker.GetInputElement().focus();
				}
				else
				{
					Grocy.Components.ProductPicker.HideCustomError();
					Grocy.FrontendHelpers.ValidateForm('transfer-form');
					$('#display_amount').focus();
				}
			},
			function(xhr)
			{
				console.error(xhr);
			}
		);
	}
});

$('#display_amount').val(parseFloat(Grocy.UserSettings.stock_default_transfer_amount));
$(".input-group-productamountpicker").trigger("change");
Grocy.FrontendHelpers.ValidateForm('transfer-form');
RefreshLocaleNumberInput();

$("#location_id_from").on('change', function(e)
{
	var locationId = $(e.target).val();
	var sumValue = 0;
	var stockId = null;

	if (locationId == $("#location_id_to").val())
	{
		$("#location_id_to").val("");
	}

	if (GetUriParam("embedded") !== undefined)
	{
		stockId = GetUriParam('stockId');
	}

	$("#specific_stock_entry").find("option").remove().end().append("<option></option>");
	if ($("#use_specific_stock_entry").is(":checked") && GetUriParam("stockId") == null)
	{
		$("#use_specific_stock_entry").click();
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
							text: __t("Amount: %1$s; Due on %2$s; Bought on %3$s", stockEntry.amount, moment(stockEntry.best_before_date).format("YYYY-MM-DD"), moment(stockEntry.purchased_date).format("YYYY-MM-DD")) + "; " + openTxt
						}));
						if (stockEntry.stock_id == stockId)
						{
							$("#specific_stock_entry").val(stockId);
						}
						sumValue = sumValue + parseFloat(stockEntry.amount);
					}
				});
				$("#display_amount").attr("max", sumValue * $("#qu_id option:selected").attr("data-qu-factor"));
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
});

$("#location_id_to").on('change', function(e)
{
	var locationId = $(e.target).val();

	if (locationId == $("#location_id_from").val())
	{
		$("#location_id_to").parent().find(".invalid-feedback").text(__t('This cannot be the same as the "From" location'));
		$("#location_id_to").val("");
	}
});

$("#qu_id").on('change', function(e)
{
	$("#display_amount").attr("max", parseFloat($('#display_amount').attr("data-stock-amount")) * $("#qu_id option:selected").attr("data-qu-factor"));
});

$('#display_amount').on('focus', function(e)
{
	$(this).select();
});

$('#transfer-form input').keyup(function(event)
{
	Grocy.FrontendHelpers.ValidateForm('transfer-form');
});

$('#transfer-form select').change(function(event)
{
	Grocy.FrontendHelpers.ValidateForm('transfer-form');
});

$('#transfer-form input').keydown(function(event)
{
	if (event.keyCode === 13) //Enter
	{
		event.preventDefault();

		if (document.getElementById('transfer-form').checkValidity() === false) //There is at least one validation error
		{
			return false;
		}
		else
		{
			$('#save-transfer-button').click();
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
					if (stockEntry.location_id == $("#location_id_from").val() || stockEntry.location_id == "")
					{
						sumValue = sumValue + parseFloat(stockEntry.amount);
					}
				});
				$("#display_amount").attr("max", sumValue * $("#qu_id option:selected").attr("data-qu-factor"));
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
		$("#location_id_from").trigger('change');
	}

	Grocy.FrontendHelpers.ValidateForm("transfer-form");
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

		$("#location_id_from").val(locationId);
		$("#location_id_from").trigger('change');
		$("#use_specific_stock_entry").click();
		$("#use_specific_stock_entry").trigger('change');
		Grocy.Components.ProductPicker.GetPicker().trigger('change');
	}
}

// Default input field
Grocy.Components.ProductPicker.GetInputElement().focus();
