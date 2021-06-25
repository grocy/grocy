import { BoolVal } from '../helpers/extensions';
import { WindowMessageBag } from '../helpers/messagebag';

function consumeView(Grocy, scope = null)
{
	var $scope = $;
	var top = scope != null ? $(scope) : $(document);

	if (scope != null)
	{
		$scope = (selector) => $(scope).find(selector);
	}

	var productamountpicker = Grocy.Use("productamountpicker");
	var productcard = null;
	if (!Grocy.GetUriParam("embedded"))
		productcard = Grocy.Use("productcard");
	var productpicker = Grocy.Use("productpicker");
	var recipepicker = Grocy.Use("recipepicker");

	$scope('#save-consume-button').on('click', function(e)
	{
		e.preventDefault();

		if ($scope(".combobox-menu-visible").length)
		{
			return;
		}

		var jsonForm = $scope('#consume-form').serializeJSON();
		Grocy.FrontendHelpers.BeginUiBusy("consume-form");

		var apiUrl = 'stock/products/' + jsonForm.product_id + '/consume';

		var jsonData = {};
		jsonData.amount = jsonForm.amount;
		jsonData.exact_amount = $scope('#consume-exact-amount').is(':checked');
		jsonData.spoiled = $scope('#spoiled').is(':checked');
		jsonData.allow_subproduct_substitution = true;

		if ($scope("#use_specific_stock_entry").is(":checked"))
		{
			jsonData.stock_entry_id = jsonForm.specific_stock_entry;
		}

		if (Grocy.FeatureFlags.GROCY_FEATURE_FLAG_STOCK_LOCATION_TRACKING)
		{
			jsonData.location_id = $scope("#location_id").val();
		}

		if (Grocy.FeatureFlags.GROCY_FEATURE_FLAG_RECIPES && recipepicker.GetValue().toString().length > 0)
		{
			jsonData.recipe_id = recipepicker.GetValue();
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
									window.history.replaceState({}, document.title, U("/consume"));
								},
								function(xhr)
								{
									Grocy.FrontendHelpers.EndUiBusy("consume-form");
									Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response);
								}
							);
						}

						$scope("#specific_stock_entry").find("option").remove().end().append("<option></option>");
						if ($scope("#use_specific_stock_entry").is(":checked"))
						{
							$scope("#use_specific_stock_entry").click();
						}

						var successMessage = null;
						if (productDetails.product.enable_tare_weight_handling == 1 && !jsonData.exact_amount)
						{
							successMessage = __t('Removed %1$s of %2$s from stock', Math.abs(jsonForm.amount - (parseFloat(productDetails.product.tare_weight) + parseFloat(productDetails.stock_amount))) + " " + __n(jsonForm.amount, productDetails.quantity_unit_stock.name, productDetails.quantity_unit_stock.name_plural), productDetails.product.name) + '<br><a class="btn btn-secondary btn-sm mt-2" href="#" onclick="Grocy.UndoStockTransaction(\'' + bookingResponse[0].transaction_id + '\')"><i class="fas fa-undo"></i> ' + __t("Undo") + '</a>';
						}
						else
						{
							successMessage = __t('Removed %1$s of %2$s from stock', Math.abs(jsonForm.amount) + " " + __n(jsonForm.amount, productDetails.quantity_unit_stock.name, productDetails.quantity_unit_stock.name_plural), productDetails.product.name) + '<br><a class="btn btn-secondary btn-sm mt-2" href="#" onclick="Grocy.UndoStockTransaction(\'' + bookingResponse[0].transaction_id + '\')"><i class="fas fa-undo"></i> ' + __t("Undo") + '</a>';
						}

						if (Grocy.GetUriParam("embedded") !== undefined)
						{
							window.parent.postMessage(WindowMessageBag("ProductChanged", jsonForm.product_id), Grocy.BaseUrl);
							window.parent.postMessage(WindowMessageBag("ShowSuccessMessage", successMessage), Grocy.BaseUrl);
							window.parent.postMessage(WindowMessageBag("CloseAllModals"), Grocy.BaseUrl);
						}
						else
						{
							Grocy.FrontendHelpers.EndUiBusy("consume-form");
							toastr.success(successMessage);
							productpicker.FinishFlow();

							productamountpicker.Reset();
							$scope("#display_amount").attr("min", Grocy.DefaultMinAmount);
							$scope("#display_amount").removeAttr("max");
							if (BoolVal(Grocy.UserSettings.stock_default_consume_amount_use_quick_consume_amount))
							{
								$scope('#display_amount').val(productDetails.product.quick_consume_amount);
							}
							else
							{
								$scope('#display_amount').val(parseFloat(Grocy.UserSettings.stock_default_consume_amount));
							}
							RefreshLocaleNumberInput();
							$scope(".input-group-productamountpicker").trigger("change");
							$scope("#tare-weight-handling-info").addClass("d-none");
							productpicker.Clear();
							if (Grocy.FeatureFlags.GROCY_FEATURE_FLAG_RECIPES)
							{
								recipepicker.Clear();
							}
							if (Grocy.FeatureFlags.GROCY_FEATURE_FLAG_STOCK_LOCATION_TRACKING)
							{
								$scope("#location_id").find("option").remove().end().append("<option></option>");
							}
							productpicker.GetInputElement().focus();
							if (productcard != null) productcard.Refresh(jsonForm.product_id);
							Grocy.FrontendHelpers.ValidateForm('consume-form');
							$scope("#consume-exact-amount-group").addClass("d-none");
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

	$scope('#save-mark-as-open-button').on('click', function(e)
	{
		e.preventDefault();

		if ($scope(".combobox-menu-visible").length)
		{
			return;
		}

		var jsonForm = $scope('#consume-form').serializeJSON();
		Grocy.FrontendHelpers.BeginUiBusy("consume-form");

		var apiUrl = 'stock/products/' + jsonForm.product_id + '/open';

		var jsonData = {};
		jsonData.amount = jsonForm.amount;
		jsonData.allow_subproduct_substitution = true;

		if ($scope("#use_specific_stock_entry").is(":checked"))
		{
			jsonData.stock_entry_id = jsonForm.specific_stock_entry;
		}

		Grocy.Api.Get('stock/products/' + jsonForm.product_id,
			function(productDetails)
			{
				Grocy.Api.Post(apiUrl, jsonData,
					function(result)
					{
						$scope("#specific_stock_entry").find("option").remove().end().append("<option></option>");
						if ($scope("#use_specific_stock_entry").is(":checked"))
						{
							$scope("#use_specific_stock_entry").click();
						}

						Grocy.FrontendHelpers.EndUiBusy("consume-form");
						toastr.success(__t('Marked %1$s of %2$s as opened', parseFloat(jsonForm.amount).toLocaleString({ minimumFractionDigits: 0, maximumFractionDigits: Grocy.UserSettings.stock_decimal_places_amounts }) + " " + __n(jsonForm.amount, productDetails.quantity_unit_stock.name, productDetails.quantity_unit_stock.name_plural), productDetails.product.name) + '<br><a class="btn btn-secondary btn-sm mt-2" href="#" onclick="Grocy.UndoStockTransaction(\'' + result[0].transaction_id + '\')"><i class="fas fa-undo"></i> ' + __t("Undo") + '</a>');

						if (BoolVal(Grocy.UserSettings.stock_default_consume_amount_use_quick_consume_amount))
						{
							$scope('#display_amount').val(productDetails.product.quick_consume_amount);
						}
						else
						{
							$scope('#display_amount').val(parseFloat(Grocy.UserSettings.stock_default_consume_amount));
						}
						RefreshLocaleNumberInput();
						$scope(".input-group-productamountpicker").trigger("change");
						productpicker.Clear();
						productpicker.GetInputElement().focus();
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
	$scope("#location_id").on('change', function(e)
	{
		var locationId = $scope(e.target).val();
		sumValue = 0;
		var stockId = null;

		$scope("#specific_stock_entry").find("option").remove().end().append("<option></option>");
		if ($scope("#use_specific_stock_entry").is(":checked"))
		{
			$scope("#use_specific_stock_entry").click();
		}

		if (Grocy.GetUriParam("embedded") !== undefined)
		{
			stockId = Grocy.GetUriParam('stockId');
		}
		else
		{
			// try to get stock id from grocycode
			if ($scope("#product_id").data("grocycode"))
			{
				var gc = $scope("#product_id").attr("barcode").split(":");
				if (gc.length == 4)
				{
					stockId = gc[3];
				}
			}
		}

		if (locationId)
		{
			Grocy.Api.Get("stock/products/" + productpicker.GetValue() + '/entries?include_sub_products=true',
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

							sumValue = sumValue + parseFloat(stockEntry.amount || 0);

							if (stockEntry.stock_id == stockId)
							{
								$scope("#use_specific_stock_entry").click();
								$scope("#specific_stock_entry").val(stockId);
							}
						}
					});

					Grocy.Api.Get('stock/products/' + productpicker.GetValue(),
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

					if (BoolVal(Grocy.UserSettings.scan_mode_consume_enabled) &&
						$scope('#product_id').attr("barcode") == "null")
					{

						Grocy.FrontendHelpers.ScanModeSubmit();
					}
				},
				function(xhr)
				{
					console.error(xhr);
				}
			);
		}
	});

	productpicker.GetPicker().on('change', function(e)
	{
		if (BoolVal(Grocy.UserSettings.scan_mode_consume_enabled))
		{
			Grocy.UISound.BarcodeScannerBeep();
		}

		$scope("#specific_stock_entry").find("option").remove().end().append("<option></option>");
		if ($scope("#use_specific_stock_entry").is(":checked"))
		{
			$scope("#use_specific_stock_entry").click();
		}
		$scope("#location_id").val("");

		var productId = $scope(e.target).val();

		if (productId)
		{
			if (productcard != null) productcard.Refresh(productId);

			Grocy.Api.Get('stock/products/' + productId,
				function(productDetails)
				{
					current_productDetails = productDetails;

					productamountpicker.Reload(productDetails.product.id, productDetails.quantity_unit_stock.id);
					productamountpicker.SetQuantityUnit(productDetails.quantity_unit_stock.id);

					if (BoolVal(Grocy.UserSettings.stock_default_consume_amount_use_quick_consume_amount))
					{
						$scope('#display_amount').val(productDetails.product.quick_consume_amount);
					}
					else
					{
						$scope('#display_amount').val(parseFloat(Grocy.UserSettings.stock_default_consume_amount));
					}
					RefreshLocaleNumberInput();
					$scope(".input-group-productamountpicker").trigger("change");

					$scope("#location_id").find("option").remove().end().append("<option></option>");
					Grocy.Api.Get("stock/products/" + productId + '/locations?include_sub_products=true',
						function(stockLocations)
						{
							var setDefault = 0;
							stockLocations.forEach(stockLocation =>
							{
								if (productDetails.location.id == stockLocation.location_id)
								{
									$scope("#location_id").append($("<option>", {
										value: stockLocation.location_id,
										text: stockLocation.location_name + " (" + __t("Default location") + ")"
									}));
									$scope("#location_id").val(productDetails.location.id);
									$scope("#location_id").trigger('change');
									setDefault = 1;
								}
								else
								{
									$scope("#location_id").append($("<option>", {
										value: stockLocation.location_id,
										text: stockLocation.location_name
									}));
								}

								if (setDefault == 0)
								{
									$scope("#location_id").val(stockLocation.location_id);
									$scope("#location_id").trigger('change');
								}
							});

							if ($scope('#product_id').attr("barcode") != "null")
							{
								Grocy.Api.Get('objects/product_barcodes?query[]=barcode=' + $scope('#product_id').attr("barcode"),
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
												Grocy.FrontendHelpers.ValidateForm('consume-form');
												RefreshLocaleNumberInput();
												if (BoolVal(Grocy.UserSettings.scan_mode_consume_enabled))
													Grocy.FrontendHelpers.ScanModeSubmit(false);
											}
										}
									},
									function(xhr)
									{
										console.error(xhr);
									}
								);
							}
						},
						function(xhr)
						{
							console.error(xhr);
						}
					);

					if (productDetails.product.enable_tare_weight_handling == 1)
					{
						$scope("#display_amount").attr("min", productDetails.product.tare_weight);
						$scope('#display_amount').attr('max', parseFloat(productDetails.stock_amount) + parseFloat(productDetails.product.tare_weight));
						$scope("#tare-weight-handling-info").removeClass("d-none");
					}
					else
					{
						$scope("#display_amount").attr("min", Grocy.DefaultMinAmount);
						$scope("#tare-weight-handling-info").addClass("d-none");
					}

					if ((parseFloat(productDetails.stock_amount_aggregated) || 0) === 0)
					{
						productamountpicker.Reset();
						productpicker.Clear();
						Grocy.FrontendHelpers.ValidateForm('consume-form');
						productpicker.ShowCustomError(__t('This product is not in stock'));
						productpicker.GetInputElement().focus();
					}
					else
					{
						productpicker.HideCustomError();
						Grocy.FrontendHelpers.ValidateForm('consume-form');
						$scope('#display_amount').focus();
					}

					if (productDetails.stock_amount == productDetails.stock_amount_opened || productDetails.product.enable_tare_weight_handling == 1)
					{
						$scope("#save-mark-as-open-button").addClass("disabled");
					}
					else
					{
						$scope("#save-mark-as-open-button").removeClass("disabled");
					}
				},
				function(xhr)
				{
					console.error(xhr);
				}
			);
		}
	});

	$scope('#display_amount').val(parseFloat(Grocy.UserSettings.stock_default_consume_amount));
	$scope(".input-group-productamountpicker").trigger("change");
	Grocy.FrontendHelpers.ValidateForm('consume-form');

	$scope('#display_amount').on('focus', function(e)
	{
		$(this).select();
	});

	$scope('#price').on('focus', function(e)
	{
		$(this).select();
	});


	$scope('#consume-form input').keyup(function(event)
	{
		Grocy.FrontendHelpers.ValidateForm('consume-form');
	});

	$scope('#consume-form select').change(function(event)
	{
		Grocy.FrontendHelpers.ValidateForm('consume-form');
	});

	$scope('#consume-form input').keydown(function(event)
	{
		if (event.keyCode === 13) //Enter
		{
			event.preventDefault();

			if ($scope('#consume-form')[0].checkValidity() === false) //There is at least one validation error
			{
				return false;
			}
			else
			{
				$scope('#save-consume-button').click();
			}
		}
	});

	$scope("#specific_stock_entry").on("change", function(e)
	{
		if ($(e.target).val() == "")
		{
			sumValue = 0;
			Grocy.Api.Get("stock/products/" + productpicker.GetValue() + '/entries?include_sub_products=true',
				function(stockEntries)
				{
					stockEntries.forEach(stockEntry =>
					{
						if (stockEntry.location_id == $scope("#location_id").val() || stockEntry.location_id == "")
						{
							sumValue = sumValue + parseFloat(stockEntry.amount_aggregated);
						}
					});
					$scope("#display_amount").attr("max", sumValue);
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
			//                                    vvv might break
			$scope("#display_amount").attr("max", $('option:selected', this).attr('amount'));
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
			$scope("#location_id").trigger('change');
		}

		Grocy.FrontendHelpers.ValidateForm("consume-form");
	});

	$scope("#qu_id").on("change", function()
	{
		RefreshForm();
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
			$scope("#location_id").val(locationId);
			$scope("#location_id").trigger('change');
			$scope("#use_specific_stock_entry").click();
			$scope("#use_specific_stock_entry").trigger('change');
		}
	}

	// Default input field
	productpicker.GetInputElement().focus();

	top.on("change", "#scan-mode", function(e)
	{
		if ($(this).prop("checked"))
		{
			Grocy.UISound.AskForPermission();
		}
	});

	$scope("#scan-mode-button").on("click", function(e)
	{
		document.activeElement.blur();
		$scope("#scan-mode").click();
		$scope("#scan-mode-button").toggleClass("btn-success").toggleClass("btn-danger");
		if ($scope("#scan-mode").prop("checked"))
		{
			$scope("#scan-mode-status").text(__t("on"));
		}
		else
		{
			$scope("#scan-mode-status").text(__t("off"));
		}
	});

	$scope('#consume-exact-amount').on('change', RefreshForm);
	var current_productDetails;
	function RefreshForm()
	{
		var productDetails = current_productDetails;
		if (!productDetails)
		{
			return;
		}

		if (productDetails.product.enable_tare_weight_handling == 1)
		{
			$scope("#consume-exact-amount-group").removeClass("d-none");
		}
		else
		{
			$scope("#consume-exact-amount-group").addClass("d-none");
		}

		if (productDetails.product.enable_tare_weight_handling == 1 && !$scope('#consume-exact-amount').is(':checked'))
		{
			$scope("#display_amount").attr("min", productDetails.product.tare_weight);
			$scope('#display_amount').attr('max', sumValue + parseFloat(productDetails.product.tare_weight));
			$scope("#tare-weight-handling-info").removeClass("d-none");
		}
		else
		{
			$scope("#tare-weight-handling-info").addClass("d-none");

			$scope("#display_amount").attr("min", Grocy.DefaultMinAmount);
			$scope('#display_amount').attr('max', sumValue * $scope("#qu_id option:selected").attr("data-qu-factor"));

			if (sumValue == 0)
			{
				$scope("#display_amount").parent().find(".invalid-feedback").text(__t('There are no units available at this location'));
			}
		}

		Grocy.FrontendHelpers.ValidateForm("consume-form");
	}
}



window.consumeView = consumeView
