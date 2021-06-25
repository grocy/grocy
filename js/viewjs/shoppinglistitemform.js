import { WindowMessageBag } from '../helpers/messagebag';

function shoppinglistitemformView(Grocy, scope = null)
{
	var $scope = $;
	if (scope != null)
	{
		$scope = (selector) => $(scope).find(selector);
	}

	var productamountpicker = Grocy.Use("productamountpicker");
	var userfields = Grocy.Use("userfieldsform");
	var productpicker = Grocy.Use("productpicker");

	Grocy.ShoppingListItemFormInitialLoadDone = false;

	$scope('#save-shoppinglist-button').on('click', function(e)
	{
		e.preventDefault();

		if ($scope(".combobox-menu-visible").length)
		{
			return;
		}

		var jsonData = $scope('#shoppinglist-form').serializeJSON();
		if (!jsonData.product_id)
		{
			jsonData.amount = jsonData.display_amount;
		}
		delete jsonData.display_amount;

		Grocy.FrontendHelpers.BeginUiBusy("shoppinglist-form");

		if (Grocy.GetUriParam("updateexistingproduct") !== undefined)
		{
			jsonData.product_amount = jsonData.amount;
			delete jsonData.amount;

			Grocy.Api.Post('stock/shoppinglist/add-product', jsonData,
				function(result)
				{
					Grocy.EditObjectId = result.created_object_id;
					userfields.Save();

					if (Grocy.GetUriParam("embedded") !== undefined)
					{
						Grocy.Api.Get('stock/products/' + jsonData.product_id,
							function(productDetails)
							{
								window.parent.postMessage(WindowMessageBag("ShowSuccessMessage", __t("Added %1$s of %2$s to the shopping list \"%3$s\"", parseFloat(jsonData.product_amount).toLocaleString({ minimumFractionDigits: 0, maximumFractionDigits: Grocy.UserSettings.stock_decimal_places_amounts }) + " " + __n(jsonData.product_amount, productDetails.default_quantity_unit_purchase.name, productDetails.default_quantity_unit_purchase.name_plural), productDetails.product.name, $scope("#shopping_list_id option:selected").text())), Grocy.BaseUrl);
								window.parent.postMessage(WindowMessageBag("ShoppingListChanged", $scope("#shopping_list_id").val().toString()), Grocy.BaseUrl);
								window.parent.postMessage(WindowMessageBag("CloseAllModals"), Grocy.BaseUrl);
							},
							function(xhr)
							{
								console.error(xhr);
							}
						);
					}
					else
					{
						window.location.href = U('/shoppinglist?list=' + $scope("#shopping_list_id").val().toString());
					}
				},
				function(xhr)
				{
					Grocy.FrontendHelpers.EndUiBusy("shoppinglist-form");
					console.error(xhr);
				}
			);
		}
		else if (Grocy.EditMode === 'create')
		{
			Grocy.Api.Post('objects/shopping_list', jsonData,
				function(result)
				{
					Grocy.EditObjectId = result.created_object_id;
					userfields.Save();

					if (Grocy.GetUriParam("embedded") !== undefined)
					{
						if (jsonData.product_id)
						{
							Grocy.Api.Get('stock/products/' + jsonData.product_id,
								function(productDetails)
								{
									window.parent.postMessage(WindowMessageBag("ShowSuccessMessage", __t("Added %1$s of %2$s to the shopping list \"%3$s\"", parseFloat(jsonData.amount).toLocaleString({ minimumFractionDigits: 0, maximumFractionDigits: Grocy.UserSettings.stock_decimal_places_amounts }) + " " + __n(jsonData.amount, productDetails.default_quantity_unit_purchase.name, productDetails.default_quantity_unit_purchase.name_plural), productDetails.product.name, $scope("#shopping_list_id option:selected").text())), Grocy.BaseUrl);
									window.parent.postMessage(WindowMessageBag("ShoppingListChanged", $scope("#shopping_list_id").val().toString()), Grocy.BaseUrl);
									window.parent.postMessage(WindowMessageBag("CloseAllModals"), Grocy.BaseUrl);
								},
								function(xhr)
								{
									console.error(xhr);
								}
							);
						}
						else
						{
							window.parent.postMessage(WindowMessageBag("ShoppingListChanged", $scope("#shopping_list_id").val().toString()), Grocy.BaseUrl);
							window.parent.postMessage(WindowMessageBag("CloseAllModals"), Grocy.BaseUrl);
						}
					}
					else
					{
						window.location.href = U('/shoppinglist?list=' + $scope("#shopping_list_id").val().toString());
					}
				},
				function(xhr)
				{
					Grocy.FrontendHelpers.EndUiBusy("shoppinglist-form");
					console.error(xhr);
				}
			);
		}
		else
		{
			Grocy.Api.Put('objects/shopping_list/' + Grocy.EditObjectId, jsonData,
				function(result)
				{
					userfields.Save();

					if (Grocy.GetUriParam("embedded") !== undefined)
					{
						if (jsonData.product_id)
						{
							Grocy.Api.Get('stock/products/' + jsonData.product_id,
								function(productDetails)
								{
									window.parent.postMessage(WindowMessageBag("ShowSuccessMessage", __t("Added %1$s of %2$s to the shopping list \"%3$s\"", parseFloat(jsonData.amount).toLocaleString({ minimumFractionDigits: 0, maximumFractionDigits: Grocy.UserSettings.stock_decimal_places_amounts }) + " " + __n(jsonData.amount, productDetails.default_quantity_unit_purchase.name, productDetails.default_quantity_unit_purchase.name_plural), productDetails.product.name, $scope("#shopping_list_id option:selected").text())), Grocy.BaseUrl);
									window.parent.postMessage(WindowMessageBag("ShoppingListChanged", $scope("#shopping_list_id").val().toString()), Grocy.BaseUrl);
									window.parent.postMessage(WindowMessageBag("CloseAllModals"), Grocy.BaseUrl);
								},
								function(xhr)
								{
									console.error(xhr);
								}
							);
						}
						else
						{
							window.parent.postMessage(WindowMessageBag("ShoppingListChanged", $scope("#shopping_list_id").val().toString()), Grocy.BaseUrl);
							window.parent.postMessage(WindowMessageBag("CloseAllModals"), Grocy.BaseUrl);
						}
					}
					else
					{
						window.location.href = U('/shoppinglist?list=' + $scope("#shopping_list_id").val().toString());
					}
				},
				function(xhr)
				{
					Grocy.FrontendHelpers.EndUiBusy("shoppinglist-form");
					console.error(xhr);
				}
			);
		}
	});

	productpicker.GetPicker().on('change', function(e)
	{
		var productId = $scope(e.target).val();

		if (productId)
		{
			Grocy.Api.Get('stock/products/' + productId,
				function(productDetails)
				{
					if (!Grocy.ShoppingListItemFormInitialLoadDone)
					{
						productamountpicker.Reload(productDetails.product.id, productDetails.quantity_unit_stock.id, true);
					}
					else
					{
						productamountpicker.Reload(productDetails.product.id, productDetails.quantity_unit_stock.id);
						productamountpicker.SetQuantityUnit(productDetails.default_quantity_unit_purchase.id);
					}

					if ($scope("#display_amount").val().toString().isEmpty())
					{
						$scope("#display_amount").val(1);
						$scope("#display_amount").trigger("change");
					}

					$scope('#display_amount').focus();
					Grocy.FrontendHelpers.ValidateForm('shoppinglist-form');
					Grocy.ShoppingListItemFormInitialLoadDone = true;
				},
				function(xhr)
				{
					console.error(xhr);
				}
			);
		}

		$scope("#note").trigger("input");
		$scope("#product_id").trigger("input");
	});

	Grocy.FrontendHelpers.ValidateForm('shoppinglist-form');
	productpicker.GetInputElement().focus();

	if (Grocy.EditMode === "edit")
	{
		productpicker.GetPicker().trigger('change');
	}

	if (Grocy.EditMode == "create")
	{
		Grocy.ShoppingListItemFormInitialLoadDone = true;
	}

	$scope('#display_amount').on('focus', function(e)
	{
		$(this).select();
	});

	$scope('#shoppinglist-form input').keyup(function(event)
	{
		Grocy.FrontendHelpers.ValidateForm('shoppinglist-form');
	});

	$scope('#shoppinglist-form input').keydown(function(event)
	{
		if (event.keyCode === 13) //Enter
		{
			event.preventDefault();

			if ($scope('#shoppinglist-form')[0].checkValidity() === false) //There is at least one validation error
			{
				return false;
			}
			else
			{
				$scope('#save-shoppinglist-button').click();
			}
		}
	});

	if (Grocy.GetUriParam("list") !== undefined)
	{
		$scope("#shopping_list_id").val(Grocy.GetUriParam("list"));
	}

	if (Grocy.GetUriParam("amount") !== undefined)
	{
		$scope("#display_amount").val(parseFloat(Grocy.GetUriParam("amount")));
		RefreshLocaleNumberInput();
		$scope(".input-group-productamountpicker").trigger("change");
		Grocy.FrontendHelpers.ValidateForm('shoppinglist-form');
	}

	if (Grocy.GetUriParam("embedded") !== undefined)
	{
		if (Grocy.GetUriParam("product") !== undefined)
		{
			productpicker.GetPicker().trigger('change');
			$scope("#display_amount").focus();
		}
		else
		{
			productpicker.GetInputElement().focus();
		}
	}

	var eitherRequiredFields = $scope("#product_id,#product_id_text_input,#note");
	eitherRequiredFields.prop('required', "");
	eitherRequiredFields.on('input', function()
	{
		eitherRequiredFields.not(this).prop('required', !$(this).val().length);
		Grocy.FrontendHelpers.ValidateForm('shoppinglist-form', $scope);
	});


	if (Grocy.GetUriParam("product-name") != null)
	{
		productpicker.GetPicker().trigger('change');
	}

	userfields.Load();

}




window.shoppinglistitemformView = shoppinglistitemformView
