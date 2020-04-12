$('#save-shoppinglist-button').on('click', function(e)
{
	e.preventDefault();

	var jsonData = $('#shoppinglist-form').serializeJSON();
	Grocy.FrontendHelpers.BeginUiBusy("shoppinglist-form");

	if (GetUriParam("updateexistingproduct") !== undefined)
	{
		jsonData.product_amount = jsonData.amount;
		delete jsonData.amount;

		Grocy.Api.Post('stock/shoppinglist/add-product', jsonData,
			function(result)
			{
				if (GetUriParam("embedded") !== undefined)
				{
					Grocy.Api.Get('stock/products/' + jsonData.product_id,
						function (productDetails)
						{
							window.parent.postMessage(WindowMessageBag("ShowSuccessMessage", __t("Added %1$s of %2$s to the shopping list \"%3$s\"", jsonData.product_amount + " " + __n(jsonData.product_amount, productDetails.quantity_unit_purchase.name, productDetails.quantity_unit_purchase.name_plural), productDetails.product.name, $("#shopping_list_id option:selected").text())), Grocy.BaseUrl);
							window.parent.postMessage(WindowMessageBag("CloseAllModals"), Grocy.BaseUrl);
						},
						function (xhr)
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
				if (GetUriParam("embedded") !== undefined)
				{
					Grocy.Api.Get('stock/products/' + jsonData.product_id,
						function (productDetails)
						{
							window.parent.postMessage(WindowMessageBag("ShowSuccessMessage", __t("Added %1$s of %2$s to the shopping list \"%3$s\"", jsonData.amount + " " + __n(jsonData.amount, productDetails.quantity_unit_purchase.name, productDetails.quantity_unit_purchase.name_plural), productDetails.product.name, $("#shopping_list_id option:selected").text())), Grocy.BaseUrl);
							window.parent.postMessage(WindowMessageBag("CloseAllModals"), Grocy.BaseUrl);
						},
						function (xhr)
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
	else
	{
		Grocy.Api.Put('objects/shopping_list/' + Grocy.EditObjectId, jsonData,
			function(result)
			{
				if (GetUriParam("embedded") !== undefined)
				{
					Grocy.Api.Get('stock/products/' + jsonData.product_id,
						function (productDetails)
						{
							window.parent.postMessage(WindowMessageBag("ShowSuccessMessage", __t("Added %1$s of %2$s to the shopping list \"%3$s\"", jsonData.amount + " " + __n(jsonData.amount, productDetails.quantity_unit_purchase.name, productDetails.quantity_unit_purchase.name_plural), productDetails.product.name, $("#shopping_list_id option:selected").text())), Grocy.BaseUrl);
							window.parent.postMessage(WindowMessageBag("CloseAllModals"), Grocy.BaseUrl);
						},
						function (xhr)
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
});

Grocy.Components.ProductPicker.GetPicker().on('change', function(e)
{
	var productId = $(e.target).val();

	if (productId)
	{
		Grocy.Components.ProductCard.Refresh(productId);

		Grocy.Api.Get('stock/products/' + productId,
			function (productDetails)
			{
				$('#amount_qu_unit').text(productDetails.quantity_unit_purchase.name);

				if (productDetails.product.allow_partial_units_in_stock == 1)
				{
					$("#amount").attr("min", "0.01");
					$("#amount").attr("step", "0.01");
					$("#amount").parent().find(".invalid-feedback").text(__t('The amount cannot be lower than %s', 0.01.toLocaleString()));
				}
				else
				{
					$("#amount").attr("min", "1");
					$("#amount").attr("step", "1");
					$("#amount").parent().find(".invalid-feedback").text(__t('The amount cannot be lower than %s', '1'));
				}
				
				$('#amount').focus();
				Grocy.FrontendHelpers.ValidateForm('shoppinglist-form');
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
Grocy.Components.ProductPicker.GetPicker().trigger('change');

if (Grocy.EditMode === "edit")
{
	Grocy.Components.ProductPicker.GetPicker().trigger('change');
}

$('#amount').on('focus', function(e)
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
	$("#amount").val(parseFloat(GetUriParam("amount")).toLocaleString({ minimumFractionDigits: 0, maximumFractionDigits: 4 }));
	Grocy.FrontendHelpers.ValidateForm('shoppinglist-form');
}
