import { WindowMessageBag } from '../helpers/messagebag';

function transferView(Grocy, scope = null)
{
	var $scope = $;
	if (scope != null)
	{
		$scope = $(scope).find;
	}

	var productpicker = Grocy.Use("productpicker");
	var productamountpicker = Grocy.Use("productamountpicker");
	var productcard = Grocy.Use("productcard");

	$scope('#save-transfer-button').on('click', function(e)
	{
		e.preventDefault();

		if ($scope(".combobox-menu-visible").length)
		{
			return;
		}

		var jsonForm = $scope('#transfer-form').serializeJSON();
		Grocy.FrontendHelpers.BeginUiBusy("transfer-form");

		var apiUrl = 'stock/products/' + jsonForm.product_id + '/transfer';

		var jsonData = {};
		jsonData.amount = jsonForm.amount;
		jsonData.location_id_to = $scope("#location_id_to").val();
		jsonData.location_id_from = $scope("#location_id_from").val();

		if ($scope("#use_specific_stock_entry").is(":checked"))
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

						if (Grocy.GetUriParam("flow") === "InplaceAddBarcodeToExistingProduct")
						{
							var jsonDataBarcode = {};
							jsonDataBarcode.barcode = Grocy.GetUriParam("barcode");
							jsonDataBarcode.product_id = jsonForm.product_id;

							Grocy.Api.Post('objects/product_barcodes', jsonDataBarcode,
								function(result)
								{
									$scope("#flow-info-InplaceAddBarcodeToExistingProduct").addClass("d-none");
									$scope('#barcode-lookup-disabled-hint').addClass('d-none');
									$scope('#barcode-lookup-hint').removeClass('d-none');
									window.history.replaceState({}, document.title, U("/transfer"));
								},
								function(xhr)
								{
									Grocy.FrontendHelpers.EndUiBusy("transfer-form");
									Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response);
								}
							);
						}
						var amount = "";
						if (productDetails.product.enable_tare_weight_handling == 1)
						{
							amount = Math.abs(jsonForm.amount - parseFloat(productDetails.product.tare_weight)).toString();

						}
						else
						{
							amount = Math.abs(jsonForm.amount).toString()
						}

						amount += " " +
							__n(jsonForm.amount,
								productDetails.quantity_unit_stock.name,
								productDetails.quantity_unit_stock.name_plural
							);

						var successMessage = __t('Transfered %1$s of %2$s from %3$s to %4$s',
							amount,
							productDetails.product.name,
							$scope('option:selected', "#location_id_from").text(),
							$scope('option:selected', "#location_id_to").text()
						) + '<br><a class="btn btn-secondary btn-sm mt-2" href="#" onclick="Grocy.UndoStockTransaction(\'' + bookingResponse[0].transaction_id + '\')"><i class="fas fa-undo"></i> ' + __t("Undo") + '</a>';

						if (Grocy.GetUriParam("embedded") !== undefined)
						{
							window.parent.postMessage(WindowMessageBag("ProductChanged", jsonForm.product_id), Grocy.BaseUrl);
							window.parent.postMessage(WindowMessageBag("ShowSuccessMessage", successMessage), Grocy.BaseUrl);
							window.parent.postMessage(WindowMessageBag("CloseAllModals"), Grocy.BaseUrl);
						}
						else
						{
							Grocy.FrontendHelpers.EndUiBusy("transfer-form");
							toastr.success(successMessage);
							productpicker.FinishFlow();

							if (parseInt($scope("#location_id_from option:selected").attr("data-is-freezer")) === 0 && parseInt($scope("#location_id_to option:selected").attr("data-is-freezer")) === 1) // Frozen
							{
								toastr.info('<span>' + __t("Frozen") + "</span> <i class='fas fa-snowflake'></i>");
							}
							if (parseInt($scope("#location_id_from option:selected").attr("data-is-freezer")) === 1 && parseInt($scope("#location_id_to option:selected").attr("data-is-freezer")) === 0) // Thawed
							{
								toastr.info('<span>' + __t("Thawed") + "</span> <i class='fas fa-fire-alt'></i>");
							}

							$scope("#specific_stock_entry").find("option").remove().end().append("<option></option>");
							$scope("#specific_stock_entry").attr("disabled", "");
							$scope("#specific_stock_entry").removeAttr("required");
							if ($scope("#use_specific_stock_entry").is(":checked"))
							{
								$scope("#use_specific_stock_entry").click();
							}

							productamountpicker.Reset();
							$scope("#location_id_from").find("option").remove().end().append("<option></option>");
							$scope("#display_amount").attr("min", Grocy.DefaultMinAmount);
							$scope("#display_amount").removeAttr("max");
							$scope('#display_amount').val(parseFloat(Grocy.UserSettings.stock_default_transfer_amount));
							RefreshLocaleNumberInput();
							$scope(".input-group-productamountpicker").trigger("change");
							$scope("#tare-weight-handling-info").addClass("d-none");
							productpicker.Clear();
							$scope("#location_id_to").val("");
							$scope("#location_id_from").val("");
							productpicker.GetInputElement().focus();
							productcard.Refresh(jsonForm.product_id);
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

	productpicker.GetPicker().on('change', function(e)
	{
		$scope("#specific_stock_entry").find("option").remove().end().append("<option></option>");
		if ($scope("#use_specific_stock_entry").is(":checked") && Grocy.GetUriParam("stockId") == null)
		{
			$scope("#use_specific_stock_entry").click();
		}
		$scope("#location_id_to").val("");
		if (Grocy.GetUriParam("stockId") == null)
		{
			$scope("#location_id_from").val("");
		}

		var productId = $scope(e.target).val();

		if (productId)
		{
			productcard.Refresh(productId);

			Grocy.Api.Get('stock/products/' + productId,
				function(productDetails)
				{
					productamountpicker.Reload(productDetails.product.id, productDetails.quantity_unit_stock.id);
					productamountpicker.SetQuantityUnit(productDetails.quantity_unit_stock.id);

					if (productDetails.product.enable_tare_weight_handling == 1)
					{
						productpicker.GetPicker().parent().find(".invalid-feedback").text(__t('Products with tare weight enabled are currently not supported for transfer'));
						productpicker.Clear();
						return;
					}

					$scope("#location_id_from").find("option").remove().end().append("<option></option>");
					Grocy.Api.Get("stock/products/" + productId + '/locations',
						function(stockLocations)
						{
							var setDefault = 0;
							stockLocations.forEach(stockLocation =>
							{
								if (productDetails.location.id == stockLocation.location_id)
								{
									$scope("#location_id_from").append($scope("<option>", {
										value: stockLocation.location_id,
										text: stockLocation.location_name + " (" + __t("Default location") + ")",
										"data-is-freezer": stockLocation.location_is_freezer
									}));
									$scope("#location_id_from").val(productDetails.location.id);
									$scope("#location_id_from").trigger('change');
									setDefault = 1;
								}
								else
								{
									$scope("#location_id_from").append($scope("<option>", {
										value: stockLocation.location_id,
										text: stockLocation.location_name,
										"data-is-freezer": stockLocation.location_is_freezer
									}));
								}

								if (setDefault == 0)
								{
									$scope("#location_id_from").val(stockLocation.location_id);
									$scope("#location_id_from").trigger('change');
								}
							});

							if (Grocy.GetUriParam("locationId") != null)
							{
								$scope("#location_id_from").val(Grocy.GetUriParam("locationId"));
								$scope("#location_id_from").trigger("change");
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
											$scope("#display_amount").val(barcode.amount);
											$scope("#display_amount").select();
										}

										if (barcode.qu_id != null)
										{
											productamountpicker.SetQuantityUnit(barcode.qu_id);
										}

										$scope(".input-group-productamountpicker").trigger("change");
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
						$scope("#display_amount").attr("min", productDetails.product.tare_weight);
						$scope("#tare-weight-handling-info").removeClass("d-none");
					}
					else
					{
						$scope("#display_amount").attr("min", Grocy.DefaultMinAmount);
						$scope("#tare-weight-handling-info").addClass("d-none");
					}

					$scope('#display_amount').attr("data-stock-amount", productDetails.stock_amount);

					if ((parseFloat(productDetails.stock_amount) || 0) === 0)
					{
						productpicker.Clear();
						Grocy.FrontendHelpers.ValidateForm('transfer-form');
						productpicker.ShowCustomError(__t('This product is not in stock'));
						productpicker.GetInputElement().focus();
					}
					else
					{
						productpicker.HideCustomError();
						Grocy.FrontendHelpers.ValidateForm('transfer-form');
						$scope('#display_amount').focus();
					}
				},
				function(xhr)
				{
					console.error(xhr);
				}
			);
		}
	});

	$scope('#display_amount').val(parseFloat(Grocy.UserSettings.stock_default_transfer_amount));
	$scope(".input-group-productamountpicker").trigger("change");
	Grocy.FrontendHelpers.ValidateForm('transfer-form');
	RefreshLocaleNumberInput();

	$scope("#location_id_from").on('change', function(e)
	{
		var locationId = $scope(e.target).val();
		var sumValue = 0;
		var stockId = null;

		if (locationId == $scope("#location_id_to").val())
		{
			$scope("#location_id_to").val("");
		}

		if (Grocy.GetUriParam("embedded") !== undefined)
		{
			stockId = Grocy.GetUriParam('stockId');
		}

		$scope("#specific_stock_entry").find("option").remove().end().append("<option></option>");
		if ($scope("#use_specific_stock_entry").is(":checked") && Grocy.GetUriParam("stockId") == null)
		{
			$scope("#use_specific_stock_entry").click();
		}

		if (locationId)
		{
			Grocy.Api.Get("stock/products/" + productpicker.GetValue() + '/entries',
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
							$scope("#specific_stock_entry").append($("<option>", {
								value: stockEntry.stock_id,
								amount: stockEntry.amount,
								text: __t("Amount: %1$s; Due on %2$s; Bought on %3$s", stockEntry.amount, moment(stockEntry.best_before_date).format("YYYY-MM-DD"), moment(stockEntry.purchased_date).format("YYYY-MM-DD")) + "; " + openTxt
							}));
							if (stockEntry.stock_id == stockId)
							{
								$scope("#specific_stock_entry").val(stockId);
							}
							sumValue = sumValue + parseFloat(stockEntry.amount);
						}
					});
					$scope("#display_amount").attr("max", sumValue * $scope("#qu_id option:selected").attr("data-qu-factor"));
					if (sumValue == 0)
					{
						$scope("#display_amount").parent().find(".invalid-feedback").text(__t('There are no units available at this location'));
					}
				},
				function(xhr)
				{
					console.error(xhr);
				}
			);
		}
	});

	$scope("#location_id_to").on('change', function(e)
	{
		var locationId = $scope(e.target).val();

		if (locationId == $scope("#location_id_from").val())
		{
			$scope("#location_id_to").parent().find(".invalid-feedback").text(__t('This cannot be the same as the "From" location'));
			$scope("#location_id_to").val("");
		}
	});

	$scope("#qu_id").on('change', function(e)
	{
		$scope("#display_amount").attr("max", parseFloat($scope('#display_amount').attr("data-stock-amount")) * $scope("#qu_id option:selected").attr("data-qu-factor"));
	});

	$scope('#display_amount').on('focus', function(e)
	{
		$(this).select();
	});

	$scope('#transfer-form input').keyup(function(event)
	{
		Grocy.FrontendHelpers.ValidateForm('transfer-form');
	});

	$scope('#transfer-form select').change(function(event)
	{
		Grocy.FrontendHelpers.ValidateForm('transfer-form');
	});

	$scope('#transfer-form input').keydown(function(event)
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
				$scope('#save-transfer-button').click();
			}
		}
	});

	$scope("#specific_stock_entry").on("change", function(e)
	{
		if ($(e.target).val() == "")
		{
			var sumValue = 0;
			Grocy.Api.Get("stock/products/" + productpicker.GetValue() + '/entries',
				function(stockEntries)
				{
					stockEntries.forEach(stockEntry =>
					{
						if (stockEntry.location_id == $scope("#location_id_from").val() || stockEntry.location_id == "")
						{
							sumValue = sumValue + parseFloat(stockEntry.amount);
						}
					});
					$scope("#display_amount").attr("max", sumValue * $scope("#qu_id option:selected").attr("data-qu-factor"));
					if (sumValue == 0)
					{
						$scope("#display_amount").parent().find(".invalid-feedback").text(__t('There are no units available at this location'));
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
			$scope("#display_amount").attr("max", $scope('option:selected', this).attr('amount'));
		}
	});

	$scope("#use_specific_stock_entry").on("change", function()
	{
		var value = $(this).is(":checked");

		if (value)
		{
			$scope("#specific_stock_entry").removeAttr("disabled");
			$scope("#specific_stock_entry").attr("required", "");
		}
		else
		{
			$scope("#specific_stock_entry").attr("disabled", "");
			$scope("#specific_stock_entry").removeAttr("required");
			$scope("#specific_stock_entry").val("");
			$scope("#location_id_from").trigger('change');
		}

		Grocy.FrontendHelpers.ValidateForm("transfer-form");
	});


	if (Grocy.GetUriParam("embedded") !== undefined)
	{
		var locationId = Grocy.GetUriParam('locationId');

		if (typeof locationId === 'undefined')
		{
			productpicker.GetPicker().trigger('change');
			productpicker.GetInputElement().focus();
		}
		else
		{

			$scope("#location_id_from").val(locationId);
			$scope("#location_id_from").trigger('change');
			$scope("#use_specific_stock_entry").click();
			$scope("#use_specific_stock_entry").trigger('change');
			productpicker.GetPicker().trigger('change');
		}
	}

	// Default input field
	productpicker.GetInputElement().focus();

}




window.transferView = transferView
