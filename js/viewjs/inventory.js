import { WindowMessageBag } from '../helpers/messagebag';

function inventoryView(Grocy, scope = null)
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
	Grocy.Use("productpicker");
	Grocy.Use("productamountpicker");
	Grocy.Use("productcard");
	Grocy.Use("shoppinglocationpicker");

	$scope('#save-inventory-button').on('click', function(e)
	{
		e.preventDefault();

		if ($scope(".combobox-menu-visible").length)
		{
			return;
		}

		var jsonForm = $scope('#inventory-form').serializeJSON();
		Grocy.FrontendHelpers.BeginUiBusy("inventory-form");

		Grocy.Api.Get('stock/products/' + jsonForm.product_id,
			function(productDetails)
			{
				var price = "";
				if (!jsonForm.price.toString().isEmpty())
				{
					price = parseFloat(jsonForm.price).toFixed(Grocy.UserSettings.stock_decimal_places_prices);
				}

				var jsonData = {};
				jsonData.new_amount = jsonForm.amount;
				jsonData.best_before_date = Grocy.Components.DateTimePicker.GetValue();
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

							Grocy.Api.Post('objects/product_barcodes', jsonDataBarcode,
								function(result)
								{
									$scope("#flow-info-InplaceAddBarcodeToExistingProduct").addClass("d-none");
									$scope('#barcode-lookup-disabled-hint').addClass('d-none');
									$scope('#barcode-lookup-hint').removeClass('d-none');
									window.history.replaceState({}, document.title, U("/inventory"));
								},
								function(xhr)
								{
									Grocy.FrontendHelpers.EndUiBusy("inventory-form");
									Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response);
								}
							);
						}

						Grocy.Api.Get('stock/products/' + jsonForm.product_id,
							function(result)
							{
								var successMessage = __t('Stock amount of %1$s is now %2$s', result.product.name, result.stock_amount + " " + __n(result.stock_amount, result.quantity_unit_stock.name, result.quantity_unit_stock.name_plural)) + '<br><a class="btn btn-secondary btn-sm mt-2" href="#" onclick="Grocy.UndoStockTransaction(\'' + bookingResponse[0].transaction_id + '\')"><i class="fas fa-undo"></i> ' + __t("Undo") + '</a>';

								if (GetUriParam("embedded") !== undefined)
								{
									window.parent.postMessage(WindowMessageBag("ProductChanged", jsonForm.product_id), Grocy.BaseUrl);
									window.parent.postMessage(WindowMessageBag("ShowSuccessMessage", successMessage), Grocy.BaseUrl);
									window.parent.postMessage(WindowMessageBag("CloseAllModals"), Grocy.BaseUrl);
								}
								else
								{
									Grocy.FrontendHelpers.EndUiBusy("inventory-form");
									toastr.success(successMessage);
									Grocy.Components.ProductPicker.FinishFlow();

									Grocy.Components.ProductAmountPicker.Reset();
									$scope('#inventory-change-info').addClass('d-none');
									$scope("#tare-weight-handling-info").addClass("d-none");
									$scope("#display_amount").attr("min", "0");
									$scope('#display_amount').val('');
									$scope('#display_amount').removeAttr("data-not-equal");
									$scope(".input-group-productamountpicker").trigger("change");
									$scope('#price').val('');
									Grocy.Components.DateTimePicker.Clear();
									Grocy.Components.ProductPicker.SetValue('');
									if (Grocy.FeatureFlags.GROCY_FEATURE_FLAG_STOCK_PRICE_TRACKING)
									{
										Grocy.Components.ShoppingLocationPicker.SetValue('');
									}
									Grocy.Components.ProductPicker.GetInputElement().focus();
									Grocy.Components.ProductCard.Refresh(jsonForm.product_id);
									Grocy.FrontendHelpers.ValidateForm('inventory-form');
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
		var productId = $scope(e.target).val();

		if (productId)
		{
			Grocy.Components.ProductCard.Refresh(productId);

			Grocy.Api.Get('stock/products/' + productId,
				function(productDetails)
				{
					Grocy.Components.ProductAmountPicker.Reload(productDetails.product.id, productDetails.quantity_unit_stock.id);
					Grocy.Components.ProductAmountPicker.SetQuantityUnit(productDetails.quantity_unit_stock.id);

					$scope('#display_amount').attr("data-stock-amount", productDetails.stock_amount)
					$scope('#display_amount').attr('data-not-equal', productDetails.stock_amount * $scope("#qu_id option:selected").attr("data-qu-factor"));

					if (productDetails.product.enable_tare_weight_handling == 1)
					{
						$scope("#display_amount").attr("min", productDetails.product.tare_weight);
						$scope("#tare-weight-handling-info").removeClass("d-none");
					}
					else
					{
						$scope("#display_amount").attr("min", "0");
						$scope("#tare-weight-handling-info").addClass("d-none");
					}

					$scope('#price').val(parseFloat(productDetails.last_price));
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
											Grocy.Components.ProductAmountPicker.SetQuantityUnit(barcode.qu_id);
										}

										$scope(".input-group-productamountpicker").trigger("change");
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

					$scope('#display_amount').val(productDetails.stock_amount);
					RefreshLocaleNumberInput();
					$scope(".input-group-productamountpicker").trigger("change");
					$scope('#display_amount').focus();
					$scope('#display_amount').trigger('keyup');
				},
				function(xhr)
				{
					console.error(xhr);
				}
			);
		}
	});

	$scope('#display_amount').val('');
	$scope(".input-group-productamountpicker").trigger("change");
	Grocy.FrontendHelpers.ValidateForm('inventory-form');

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

	$scope('#display_amount').on('focus', function(e)
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

	$scope('#inventory-form input').keyup(function(event)
	{
		Grocy.FrontendHelpers.ValidateForm('inventory-form');
	});

	$scope('#inventory-form input').keydown(function(event)
	{
		if (event.keyCode === 13) //Enter
		{
			event.preventDefault();

			if (document.getElementById('inventory-form').checkValidity() === false) //There is at least one validation error
			{
				return false;
			}
			else
			{
				$scope('#save-inventory-button').click();
			}
		}
	});


	$scope('#qu_id').on('change', function(e)
	{
		$scope('#display_amount').attr('data-not-equal', parseFloat($scope('#display_amount').attr('data-stock-amount')) * parseFloat($scope("#qu_id option:selected").attr("data-qu-factor")));
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

	$scope('#display_amount').on('keyup', function(e)
	{
		var productId = Grocy.Components.ProductPicker.GetValue();
		var newAmount = parseInt($scope('#amount').val());

		if (productId)
		{
			Grocy.Api.Get('stock/products/' + productId,
				function(productDetails)
				{
					var productStockAmount = parseFloat(productDetails.stock_amount || parseFloat('0'));

					var containerWeight = parseFloat("0");
					if (productDetails.product.enable_tare_weight_handling == 1)
					{
						containerWeight = parseFloat(productDetails.product.tare_weight);
					}

					var estimatedBookingAmount = Math.abs(newAmount - productStockAmount - containerWeight);
					$scope('#inventory-change-info').removeClass('d-none');

					if (productDetails.product.enable_tare_weight_handling == 1 && newAmount < containerWeight)
					{
						$scope('#inventory-change-info').addClass('d-none');
					}
					else if (newAmount > productStockAmount + containerWeight)
					{
						$scope('#inventory-change-info').text(__t('This means %s will be added to stock', estimatedBookingAmount.toLocaleString() + ' ' + __n(estimatedBookingAmount, productDetails.quantity_unit_stock.name, productDetails.quantity_unit_stock.name_plural)));
						Grocy.Components.DateTimePicker.GetInputElement().attr('required', '');
						if (Grocy.FeatureFlags.GROCY_FEATURE_FLAG_STOCK_LOCATION_TRACKING)
						{
							Grocy.Components.LocationPicker.GetInputElement().attr('required', '');
						}
					}
					else if (newAmount < productStockAmount + containerWeight)
					{
						$scope('#inventory-change-info').text(__t('This means %s will be removed from stock', estimatedBookingAmount.toLocaleString() + ' ' + __n(estimatedBookingAmount, productDetails.quantity_unit_stock.name, productDetails.quantity_unit_stock.name_plural)));
						Grocy.Components.DateTimePicker.GetInputElement().removeAttr('required');
						if (Grocy.FeatureFlags.GROCY_FEATURE_FLAG_STOCK_LOCATION_TRACKING)
						{
							Grocy.Components.LocationPicker.GetInputElement().removeAttr('required');
						}
					}

					if (!Grocy.FeatureFlags.GROCY_FEATURE_FLAG_STOCK_BEST_BEFORE_DATE_TRACKING)
					{
						Grocy.Components.DateTimePicker.GetInputElement().removeAttr('required');
					}

					Grocy.FrontendHelpers.ValidateForm('inventory-form');
				},
				function(xhr)
				{
					console.error(xhr);
				}
			);
		}
	});

	$scope("#display_amount").attr("min", "0");

}

window.inventoryView = inventoryView