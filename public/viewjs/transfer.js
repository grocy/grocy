$('#save-transfer-button').on('click', function(e)
{
	e.preventDefault();

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

	Grocy.Api.Get('stock/products/' + jsonForm.product_id,
		function(productDetails)
		{
			Grocy.Api.Post(apiUrl, jsonData,
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
								window.history.replaceState({ }, document.title, U("/transfer"));
							},
							function(xhr)
							{
								console.error(xhr);
							}
						);
					}

					if (productDetails.product.enable_tare_weight_handling == 1)
					{
						var successMessage = __t('Transfered %1$s of %2$s stock from %3$s to %4$s', Math.abs(jsonForm.amount - parseFloat(productDetails.product.tare_weight)) + " " + __n(jsonForm.amount, productDetails.quantity_unit_stock.name, productDetails.quantity_unit_stock.name_plural), productDetails.product.name,$('option:selected', "#location_id_from").text(), $('option:selected', "#location_id_to").text()); 
					}
					else
					{
						var successMessage =__t('Transfered %1$s of %2$s stock from %3$s to %4$s', Math.abs(jsonForm.amount) + " " + __n(jsonForm.amount, productDetails.quantity_unit_stock.name, productDetails.quantity_unit_stock.name_plural), productDetails.product.name, $('option:selected', "#location_id_from").text(), $('option:selected', "#location_id_to").text());
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

						$("#specific_stock_entry").find("option").remove().end().append("<option></option>");
						$("#specific_stock_entry").attr("disabled", "");
						$("#specific_stock_entry").removeAttr("required");
						if ($("#use_specific_stock_entry").is(":checked"))
						{
							$("#use_specific_stock_entry").click();
						}

						$("#location_id_from").find("option").remove().end().append("<option></option>");
						$("#amount").attr("min", "1");
						$("#amount").attr("max", "999999");
						$("#amount").attr("step", "1");
						$("#amount").parent().find(".invalid-feedback").text(__t('The amount cannot be lower than %s', '1'));
						$('#amount').val(Grocy.UserSettings.stock_default_transfer_amount);
						$('#amount_qu_unit').text("");
						$("#tare-weight-handling-info").addClass("d-none");
						Grocy.Components.ProductPicker.Clear();
						$("#location_id_to").val("");
						$("#location_id_from").val("");
						Grocy.Components.ProductPicker.GetInputElement().focus();
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
	if ($("#use_specific_stock_entry").is(":checked"))
	{
		$("#use_specific_stock_entry").click();
	}
	$("#location_id_to").val("");
	$("#location_id_from").val("");

	var productId = $(e.target).val();

	if (productId)
	{
		Grocy.Components.ProductCard.Refresh(productId);

		Grocy.Api.Get('stock/products/' + productId,
			function(productDetails)
			{
				if (productDetails.product.enable_tare_weight_handling == 1) {
					Grocy.Components.ProductPicker.GetPicker().parent().find(".invalid-feedback").text(__t('Products with Tare weight enabled are currently not supported for Transfer. Please select another product.'));
					Grocy.Components.ProductPicker.Clear();
					return;
				}
				$('#amount_qu_unit').text(productDetails.quantity_unit_stock.name);

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
										text: __t("%1$s (default location)", stockLocation.location_name)
								}));
								$("#location_id_from").val(productDetails.location.id);
								$("#location_id_from").trigger('change');
								setDefault = 1;
							}
							else
							{
								$("#location_id_from").append($("<option>", {
									value: stockLocation.location_id,
									text: __t("%1$s", stockLocation.location_name)
								}));
							}

							if (setDefault == 0)
							{
								$("#location_id_from").val(stockLocation.location_id);
								$("#location_id_from").trigger('change');
							}
						});
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
					$("#tare-weight-handling-info").removeClass("d-none");
				}
				else
				{
					$("#tare-weight-handling-info").addClass("d-none");
				}

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
					$('#amount').focus();
				}
			},
			function(xhr)
			{
				console.error(xhr);
			}
		);
	}
});

$('#amount').val(Grocy.UserSettings.stock_default_transfer_amount);
Grocy.Components.ProductPicker.GetPicker().trigger('change');
Grocy.Components.ProductPicker.GetInputElement().focus();
Grocy.FrontendHelpers.ValidateForm('transfer-form');

$("#location_id_from").on('change', function(e)
{
	var locationId = $(e.target).val();
	var sumValue = 0;

	if (locationId == $("#location_id_to").val())
	{
		$("#location_id_to").val("");
	}

	$("#specific_stock_entry").find("option").remove().end().append("<option></option>");
	if ($("#use_specific_stock_entry").is(":checked"))
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
							text: __t("Amount: %1$s; Expires on %2$s; Bought on %3$s; Price: %4$s", stockEntry.amount, moment(stockEntry.best_before_date).format("YYYY-MM-DD"), moment(stockEntry.purchased_date).format("YYYY-MM-DD")) + "; " + openTxt
						}));
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

$('#amount').on('focus', function(e)
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
		$("#location_id_from").trigger('change');
	}

	Grocy.FrontendHelpers.ValidateForm("transfer-form");
});
