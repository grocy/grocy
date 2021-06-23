import { BoolVal } from '../helpers/extensions';
import { WindowMessageBag } from '../helpers/messagebag';

function purchaseView(Grocy, scope = null)
{
	var $scope = $;
	if (scope != null)
	{
		$scope = $(scope).find;
	}

	Grocy.Use("datetimepicker");
	if (Grocy.UserSettings.show_purchased_date_on_purchase)
	{
		Grocy.Use("datetimepicker2");
	}
	Grocy.Use("locationpicker");
	Grocy.Use("numberpicker");
	Grocy.Use("productamountpicker");
	Grocy.Use("productcard");
	Grocy.Use("shoppinglocationpicker");

	var CurrentProductDetails;

	$scope('#save-purchase-button').on('click', function(e)
	{
		e.preventDefault();

		if ($scope(".combobox-menu-visible").length)
		{
			return;
		}

		if ($scope(".combobox-menu-visible").length)
		{
			return;
		}

		var jsonForm = $scope('#purchase-form').serializeJSON();

		Grocy.FrontendHelpers.BeginUiBusy("purchase-form");

		Grocy.Api.Get('stock/products/' + jsonForm.product_id,
			function(productDetails)
			{
				var jsonData = {};
				jsonData.amount = jsonForm.amount;
				jsonData.print_stock_label = jsonForm.print_stock_label

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

					var price = parseFloat(jsonForm.price * $scope("#qu_id option:selected").attr("data-qu-factor")).toFixed(Grocy.UserSettings.stock_decimal_places_prices);
					if ($scope("input[name='price-type']:checked").val() == "total-price")
					{
						price = parseFloat(price / amount).toFixed(Grocy.UserSettings.stock_decimal_places_prices);
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
						if ($scope("#purchase-form").hasAttr("data-used-barcode"))
						{
							Grocy.Api.Put('objects/product_barcodes/' + $scope("#purchase-form").attr("data-used-barcode"), { last_price: $scope("#price").val() },
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

						if (Grocy.GetUriParam("flow") == "InplaceAddBarcodeToExistingProduct")
						{
							var jsonDataBarcode = {};
							jsonDataBarcode.barcode = Grocy.GetUriParam("barcode");
							jsonDataBarcode.product_id = jsonForm.product_id;
							jsonDataBarcode.shopping_location_id = jsonForm.shopping_location_id;

							Grocy.Api.Post('objects/product_barcodes', jsonDataBarcode,
								function(result)
								{
									$scope("#flow-info-InplaceAddBarcodeToExistingProduct").addClass("d-none");
									$scope('#barcode-lookup-disabled-hint').addClass('d-none');
									$scope('#barcode-lookup-hint').removeClass('d-none');
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
						var successMessage = __t('Added %1$s of %2$s to stock', amountMessage + " " + __n(amountMessage, productDetails.quantity_unit_stock.name, productDetails.quantity_unit_stock.name_plural), productDetails.product.name) + '<br><a class="btn btn-secondary btn-sm mt-2" href="#" onclick="Grocy.UndoStockTransaction(\'' + result[0].transaction_id + '\')"><i class="fas fa-undo"></i> ' + __t("Undo") + '</a>';

						if (Grocy.FeatureFlags.GROCY_FEATURE_FLAG_LABELPRINTER)
						{
							if (Grocy.Webhooks.labelprinter !== undefined)
							{
								var post_data = {};
								post_data.product = productDetails.product.name;
								post_data.grocycode = 'grcy:p:' + jsonForm.product_id + ":" + result[0].stock_id
								if (Grocy.FeatureFlags.GROCY_FEATURE_FLAG_STOCK_BEST_BEFORE_DATE_TRACKING)
								{
									post_data.duedate = __t('DD') + ': ' + result[0].best_before_date
								}

								if (jsonForm.print_stock_label > 0)
								{
									var reps = 1;
									if (jsonForm.print_stock_label == 2)
									{
										reps = Math.floor(jsonData.amount);
									}
									Grocy.FrontendHelpers.RunWebhook(Grocy.Webhooks.labelprinter, post_data, reps);
								}
							}
						}

						if (Grocy.GetUriParam("embedded") !== undefined)
						{
							window.parent.postMessage(WindowMessageBag("ProductChanged", jsonForm.product_id), Grocy.BaseUrl);
							window.parent.postMessage(WindowMessageBag("AfterItemAdded", Grocy.GetUriParam("listitemid")), Grocy.BaseUrl);
							window.parent.postMessage(WindowMessageBag("ShowSuccessMessage", successMessage), Grocy.BaseUrl);
							window.parent.postMessage(WindowMessageBag("Ready"), Grocy.BaseUrl);
							window.parent.postMessage(WindowMessageBag("CloseAllModals"), Grocy.BaseUrl);
						}
						else
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
							$scope("#purchase-form").removeAttr("data-used-barcode");
							$scope("#display_amount").attr("min", Grocy.DefaultMinAmount);
							$scope('#display_amount').val(parseFloat(Grocy.UserSettings.stock_default_purchase_amount));
							$scope(".input-group-productamountpicker").trigger("change");
							$scope('#price').val('');
							$scope("#tare-weight-handling-info").addClass("d-none");
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

							$scope('#price-hint').text("");
							var priceTypeUnitPrice = $scope("#price-type-unit-price");
							var priceTypeUnitPriceLabel = $scope("[for=" + priceTypeUnitPrice.attr("id") + "]");
							priceTypeUnitPriceLabel.text(__t("Unit price"));

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

			var productId = $scope(e.target).val();

			if (productId)
			{
				Grocy.Components.ProductCard.Refresh(productId);

				Grocy.Api.Get('stock/products/' + productId,
					function(productDetails)
					{
						CurrentProductDetails = productDetails;

						Grocy.Components.ProductAmountPicker.Reload(productDetails.product.id, productDetails.quantity_unit_stock.id);
						Grocy.Components.ProductAmountPicker.SetQuantityUnit(productDetails.default_quantity_unit_purchase.id);
						$scope('#display_amount').val(parseFloat(Grocy.UserSettings.stock_default_purchase_amount));
						$scope(".input-group-productamountpicker").trigger("change");

						if (Grocy.GetUriParam("flow") === "shoppinglistitemtostock")
						{
							Grocy.Components.ProductAmountPicker.SetQuantityUnit(Grocy.GetUriParam("quId"));
							$scope('#display_amount').val(parseFloat(Grocy.GetUriParam("amount") * $scope("#qu_id option:selected").attr("data-qu-factor")));
						}

						$scope(".input-group-productamountpicker").trigger("change");

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
							$scope("#price").val("")
						}
						else
						{
							$scope('#price').val(parseFloat(productDetails.last_price / $scope("#qu_id option:selected").attr("data-qu-factor")));
						}

						var priceTypeUnitPrice = $scope("#price-type-unit-price");
						var priceTypeUnitPriceLabel = $scope("[for=" + priceTypeUnitPrice.attr("id") + "]");
						priceTypeUnitPriceLabel.text($scope("#qu_id option:selected").text() + " " + __t("price"));

						refreshPriceHint();

						if (productDetails.product.enable_tare_weight_handling == 1)
						{
							var minAmount = parseFloat(productDetails.product.tare_weight) / $scope("#qu_id option:selected").attr("data-qu-factor") + parseFloat(productDetails.stock_amount);
							$scope("#display_amount").attr("min", minAmount);
							$scope("#tare-weight-handling-info").removeClass("d-none");
						}
						else
						{
							$scope("#display_amount").attr("min", Grocy.DefaultMinAmount);
							$scope("#tare-weight-handling-info").addClass("d-none");
						}

						if (Grocy.FeatureFlags.GROCY_FEATURE_FLAG_STOCK_BEST_BEFORE_DATE_TRACKING)
						{
							if (productDetails.product.default_best_before_days.toString() !== '0')
							{
								if (productDetails.product.default_best_before_days == -1)
								{
									if (!$scope("#datetimepicker-shortcut").is(":checked"))
									{
										$scope("#datetimepicker-shortcut").click();
									}
								}
								else
								{
									Grocy.Components.DateTimePicker.SetValue(moment().add(productDetails.product.default_best_before_days, 'days').format('YYYY-MM-DD'));
								}
							}
						}

						if (Grocy.FeatureFlags.GROCY_FEATURE_FLAG_LABELPRINTER)
						{
							$scope("#print_stock_label").val(productDetails.product.default_print_stock_label);
							if (productDetails.product.allow_label_per_unit)
							{
								if ($scope('#default_print_stock_label').val() == "2")
								{
									$scope("#default_print_stock_label").val("0");
								}
								$scope('#label-option-per-unit').prop("disabled", true);
							}
							else
							{
								$scope('#label-option-per-unit').prop("disabled", false);
							}
						}

						$scope("#display_amount").focus();

						Grocy.FrontendHelpers.ValidateForm('purchase-form');
						if (Grocy.GetUriParam("flow") === "shoppinglistitemtostock" && BoolVal(Grocy.UserSettings.shopping_list_to_stock_workflow_auto_submit_when_prefilled) && document.getElementById("purchase-form").checkValidity() === true)
						{
							$scope("#save-purchase-button").click();
						}

						RefreshLocaleNumberInput();
						var elem = document.getElementById("product_id");
						if (elem.getAttribute("barcode") != "null" && !elem.getAttribute("barcode").startsWith("grcy"))
						{
							Grocy.Api.Get('objects/product_barcodes?query[]=barcode=' + document.getElementById("product_id").getAttribute("barcode"),
								function(barcodeResult)
								{
									if (barcodeResult != null)
									{
										var barcode = barcodeResult[0];
										$scope("#purchase-form").attr("data-used-barcode", barcode.id);

										if (barcode != null)
										{
											if (barcode.amount != null && !barcode.amount.isEmpty())
											{
												$scope("#display_amount").val(barcode.amount);
												$scope("#display_amount").select();
											}

											if (barcode.qu_id != null)
											{
												Grocy.Components.ProductAmountPicker.SetQuantityUnit(barcode.qu_id);
											}

											if (Grocy.FeatureFlags.GROCY_FEATURE_FLAG_STOCK_PRICE_TRACKING && barcode.shopping_location_id != null)
											{
												Grocy.Components.ShoppingLocationPicker.SetId(barcode.shopping_location_id);
											}

											if (barcode.last_price != null && !barcode.last_price.isEmpty())
											{
												$scope("#price").val(barcode.last_price);
												$scope("#price-type-total-price").click();
											}

											$scope(".input-group-productamountpicker").trigger("change");
											Grocy.FrontendHelpers.ValidateForm('purchase-form');
											RefreshLocaleNumberInput();
										}
									}

									Grocy.ScanModeSubmit(false);
								},
								function(xhr)
								{
									console.error(xhr);
								}
							);
						}
						else
						{
							$scope("#purchase-form").removeAttr("data-used-barcode");
							Grocy.ScanModeSubmit();
						}

						$scope('#display_amount').trigger("keyup");
					},
					function(xhr)
					{
						console.error(xhr);
					}
				);
			}
		});
	}

	$scope('#display_amount').val(parseFloat(Grocy.UserSettings.stock_default_purchase_amount));
	RefreshLocaleNumberInput();
	$scope(".input-group-productamountpicker").trigger("change");
	Grocy.FrontendHelpers.ValidateForm('purchase-form');

	if (Grocy.Components.ProductPicker)
	{
		if (Grocy.Components.ProductPicker.InAnyFlow() === false && Grocy.GetUriParam("embedded") === undefined)
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

	$scope('#display_amount').on('focus', function(e)
	{
		if (Grocy.Components.ProductPicker.GetValue().length === 0)
		{
			Grocy.Components.ProductPicker.GetInputElement().focus();
		}
		else
		{
			$scope(this).select();
		}
	});

	$scope('#price').on('focus', function(e)
	{
		$scope(this).select();
	});

	$scope('#purchase-form input').keyup(function(event)
	{
		Grocy.FrontendHelpers.ValidateForm('purchase-form');
	});

	$scope('#purchase-form input').keydown(function(event)
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
				$scope('#save-purchase-button').click();
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

	$scope('#price').on('keyup', function(e)
	{
		refreshPriceHint();
	});

	$scope('#price-type-unit-price').on('change', function(e)
	{
		refreshPriceHint();
	});

	$scope('#price-type-total-price').on('change', function(e)
	{
		refreshPriceHint();
	});

	$scope('#display_amount').on('change', function(e)
	{
		refreshPriceHint();
		Grocy.FrontendHelpers.ValidateForm('purchase-form');
	});

	function refreshPriceHint()
	{
		if ($scope('#amount').val() == 0 || $scope('#price').val() == 0)
		{
			$scope('#price-hint').text("");
			return;
		}

		if ($scope("input[name='price-type']:checked").val() == "total-price" || $scope("#qu_id").attr("data-destination-qu-name") != $scope("#qu_id option:selected").text())
		{
			var amount = $scope('#display_amount').val();
			if (BoolVal(CurrentProductDetails.product.enable_tare_weight_handling))
			{
				amount -= parseFloat(CurrentProductDetails.product.tare_weight);
			}

			var price = parseFloat($scope('#price').val() * $scope("#qu_id option:selected").attr("data-qu-factor")).toFixed(Grocy.UserSettings.stock_decimal_places_prices);
			if ($scope("input[name='price-type']:checked").val() == "total-price")
			{
				price = parseFloat(price / amount).toFixed(Grocy.UserSettings.stock_decimal_places_prices);
			}

			$scope('#price-hint').text(__t('means %1$s per %2$s', price.toLocaleString(undefined, { style: "currency", currency: Grocy.Currency, minimumFractionDigits: Grocy.UserSettings.stock_decimal_places_prices, maximumFractionDigits: Grocy.UserSettings.stock_decimal_places_prices }), $scope("#qu_id").attr("data-destination-qu-name")));
		}
		else
		{
			$scope('#price-hint').text("");
		}
	}

	$scope("#scan-mode").on("change", function(e)
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

	$scope('#qu_id').on('change', function(e)
	{
		var priceTypeUnitPrice = $scope("#price-type-unit-price");
		var priceTypeUnitPriceLabel = $scope("[for=" + priceTypeUnitPrice.attr("id") + "]");
		priceTypeUnitPriceLabel.text($scope("#qu_id option:selected").text() + " " + __t("price"));
		refreshPriceHint();
	});

}

window.purchaseView = purchaseView