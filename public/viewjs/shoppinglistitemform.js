Grocy.ShoppingListItemFormInitialLoadDone = false;

$('#save-shoppinglist-button').on('click', function(e)
{
	e.preventDefault();

	var jsonData = $('#shoppinglist-form').serializeJSON();
	if (!jsonData.product_id)
	{
		jsonData.amount = jsonData.display_amount;
	}
	delete jsonData.display_amount;

	Grocy.FrontendHelpers.BeginUiBusy("shoppinglist-form");

	if (GetUriParam("updateexistingproduct") !== undefined)
	{
		jsonData.product_amount = jsonData.amount;
		delete jsonData.amount;

		Grocy.Api.Post('stock/shoppinglist/add-product', jsonData,
			function(result)
			{
				Grocy.EditObjectId = result.created_object_id;
				Grocy.Components.UserfieldsForm.Save();

				if (GetUriParam("embedded") !== undefined)
				{
					Grocy.Api.Get('stock/products/' + jsonData.product_id,
						function(productDetails)
						{
							window.parent.postMessage(WindowMessageBag("ShowSuccessMessage", __t("Added %1$s of %2$s to the shopping list \"%3$s\"", jsonData.product_amount + " " + __n(jsonData.product_amount, productDetails.default_quantity_unit_purchase.name, productDetails.default_.name_plural), productDetails.product.name, $("#shopping_list_id option:selected").text())), Grocy.BaseUrl);
							window.parent.postMessage(WindowMessageBag("ShoppingListChanged", $("#shopping_list_id").val().toString()), Grocy.BaseUrl);
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
					window.location.href = U('/shoppinglist?list=' + $("#shopping_list_id").val().toString());
				}
			},
			function(xhr)
			{
				Grocy.FrontendHelpers.EndUiBusy("shoppinglist-form");
				console.error(xhr);
			}
		);
	}

	if (Grocy.EditMode === 'create')
	{
		Grocy.Api.Post('objects/shopping_list', jsonData,
			function(result)
			{
				Grocy.EditObjectId = result.created_object_id;
				Grocy.Components.UserfieldsForm.Save();

				if (GetUriParam("embedded") !== undefined)
				{
					if (jsonData.product_id)
					{
						Grocy.Api.Get('stock/products/' + jsonData.product_id,
							function(productDetails)
							{
								window.parent.postMessage(WindowMessageBag("ShowSuccessMessage", __t("Added %1$s of %2$s to the shopping list \"%3$s\"", jsonData.amount + " " + __n(jsonData.amount, productDetails.default_quantity_unit_purchase.name, productDetails.default_quantity_unit_purchase.name_plural), productDetails.product.name, $("#shopping_list_id option:selected").text())), Grocy.BaseUrl);
								window.parent.postMessage(WindowMessageBag("ShoppingListChanged", $("#shopping_list_id").val().toString()), Grocy.BaseUrl);
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
						window.parent.postMessage(WindowMessageBag("ShoppingListChanged", $("#shopping_list_id").val().toString()), Grocy.BaseUrl);
						window.parent.postMessage(WindowMessageBag("CloseAllModals"), Grocy.BaseUrl);
					}
				}
				else
				{
					window.location.href = U('/shoppinglist?list=' + $("#shopping_list_id").val().toString());
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
				Grocy.Components.UserfieldsForm.Save();

				if (GetUriParam("embedded") !== undefined)
				{
					if (jsonData.product_id)
					{
						Grocy.Api.Get('stock/products/' + jsonData.product_id,
							function(productDetails)
							{
								window.parent.postMessage(WindowMessageBag("ShowSuccessMessage", __t("Added %1$s of %2$s to the shopping list \"%3$s\"", jsonData.amount + " " + __n(jsonData.amount, productDetails.default_quantity_unit_purchase.name, productDetails.default_quantity_unit_purchase.name_plural), productDetails.product.name, $("#shopping_list_id option:selected").text())), Grocy.BaseUrl);
								window.parent.postMessage(WindowMessageBag("ShoppingListChanged", $("#shopping_list_id").val().toString()), Grocy.BaseUrl);
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
						window.parent.postMessage(WindowMessageBag("ShoppingListChanged", $("#shopping_list_id").val().toString()), Grocy.BaseUrl);
						window.parent.postMessage(WindowMessageBag("CloseAllModals"), Grocy.BaseUrl);
					}
				}
				else
				{
					window.location.href = U('/shoppinglist?list=' + $("#shopping_list_id").val().toString());
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

Grocy.Components.ProductPicker.GetPicker().on('change', function(e)
{
	var productId = $(e.target).val();

	if (productId)
	{
		Grocy.Api.Get('stock/products/' + productId,
			function(productDetails)
			{
				if (!Grocy.ShoppingListItemFormInitialLoadDone)
				{
					Grocy.Components.ProductAmountPicker.Reload(productDetails.product.id, productDetails.quantity_unit_stock.id, true);
				}
				else
				{
					Grocy.Components.ProductAmountPicker.Reload(productDetails.product.id, productDetails.quantity_unit_stock.id);
					Grocy.Components.ProductAmountPicker.SetQuantityUnit(productDetails.default_quantity_unit_purchase.id);
				}

				$('#display_amount').focus();
				Grocy.FrontendHelpers.ValidateForm('shoppinglist-form');
				Grocy.ShoppingListItemFormInitialLoadDone = true;
			},
			function(xhr)
			{
				console.error(xhr);
			}
		);
	}
});

Grocy.FrontendHelpers.ValidateForm('shoppinglist-form');
Grocy.Components.ProductPicker.GetInputElement().focus();

if (Grocy.EditMode === "edit")
{
	Grocy.Components.ProductPicker.GetPicker().trigger('change');
}

if (Grocy.EditMode == "create")
{
	Grocy.ShoppingListItemFormInitialLoadDone = true;
}

$('#display_amount').on('focus', function(e)
{
	$(this).select();
});

$('#shoppinglist-form input').keyup(function(event)
{
	Grocy.FrontendHelpers.ValidateForm('shoppinglist-form');
});

$('#shoppinglist-form input').keydown(function(event)
{
	if (event.keyCode === 13) //Enter
	{
		event.preventDefault();

		if (document.getElementById('shoppinglist-form').checkValidity() === false) //There is at least one validation error
		{
			return false;
		}
		else
		{
			$('#save-shoppinglist-button').click();
		}
	}
});

if (GetUriParam("list") !== undefined)
{
	$("#shopping_list_id").val(GetUriParam("list"));
}

if (GetUriParam("amount") !== undefined)
{
	$("#display_amount").val(parseFloat(GetUriParam("amount")));
	RefreshLocaleNumberInput();
	$(".input-group-productamountpicker").trigger("change");
	Grocy.FrontendHelpers.ValidateForm('shoppinglist-form');
}

Grocy.Components.UserfieldsForm.Load();
