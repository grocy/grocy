Grocy.RecipePosFormInitialLoadDone = false;

$('#save-recipe-pos-button').on('click', function(e)
{
	e.preventDefault();

	if (!Grocy.FrontendHelpers.ValidateForm("recipe-pos-form", true))
	{
		return;
	}

	if ($(".combobox-menu-visible").length)
	{
		return;
	}

	var jsonData = $('#recipe-pos-form').serializeJSON();
	jsonData.recipe_id = Grocy.EditObjectParentId;
	delete jsonData.display_amount;

	Grocy.FrontendHelpers.BeginUiBusy("recipe-pos-form");

	if (GetUriParam("flow") === "InplaceAddBarcodeToExistingProduct")
	{
		var jsonDataBarcode = {};
		jsonDataBarcode.barcode = GetUriParam("barcode");
		jsonDataBarcode.product_id = jsonData.product_id;

		Grocy.Api.Post('objects/product_barcodes', jsonDataBarcode,
			function(result)
			{
				$("#flow-info-InplaceAddBarcodeToExistingProduct").addClass("d-none");
				$('#barcode-lookup-disabled-hint').addClass('d-none');
				$('#barcode-lookup-hint').removeClass('d-none');
			},
			function(xhr)
			{
				Grocy.FrontendHelpers.EndUiBusy("inventory-form");
				Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response);
			}
		);
	}

	if (Grocy.EditMode === 'create')
	{
		Grocy.Api.Post('objects/recipes_pos', jsonData,
			function(result)
			{
				window.parent.postMessage(WindowMessageBag("IngredientsChanged"), Grocy.BaseUrl);
				window.parent.postMessage(WindowMessageBag("CloseLastModal"), Grocy.BaseUrl);
			},
			function(xhr)
			{
				Grocy.FrontendHelpers.EndUiBusy("recipe-pos-form");
				Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response);
			}
		);
	}
	else
	{
		Grocy.Api.Put('objects/recipes_pos/' + Grocy.EditObjectId, jsonData,
			function(result)
			{
				window.parent.postMessage(WindowMessageBag("IngredientsChanged"), Grocy.BaseUrl);
				window.parent.postMessage(WindowMessageBag("CloseLastModal"), Grocy.BaseUrl);
			},
			function(xhr)
			{
				Grocy.FrontendHelpers.EndUiBusy("recipe-pos-form");
				Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response);
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
				if (!Grocy.RecipePosFormInitialLoadDone)
				{
					Grocy.Components.ProductAmountPicker.Reload(productDetails.product.id, productDetails.quantity_unit_stock.id, true);
				}
				else
				{
					Grocy.Components.ProductAmountPicker.Reload(productDetails.product.id, productDetails.quantity_unit_stock.id);
				}

				if (Grocy.Mode == "create")
				{
					$("#not_check_stock_fulfillment").prop("checked", productDetails.product.not_check_stock_fulfillment_for_recipes == 1);
				}

				if (!$("#only_check_single_unit_in_stock").prop("checked") && Grocy.RecipePosFormInitialLoadDone)
				{
					Grocy.Components.ProductAmountPicker.SetQuantityUnit(productDetails.quantity_unit_stock.id);
				}

				$('#display_amount').focus();
				Grocy.FrontendHelpers.ValidateForm('recipe-pos-form');
				Grocy.RecipePosFormInitialLoadDone = true;
			},
			function(xhr)
			{
				console.error(xhr);
			}
		);
	}
});

Grocy.FrontendHelpers.ValidateForm('recipe-pos-form');

if (!Grocy.Components.ProductPicker.InAnyFlow())
{
	if (GetUriParam("product") !== undefined || Grocy.EditMode == "edit")
	{
		Grocy.Components.ProductPicker.GetPicker().trigger('change');

		setTimeout(function()
		{
			$("#display_amount").focus();
		}, Grocy.FormFocusDelay);
	}
	else
	{
		setTimeout(function()
		{
			Grocy.Components.ProductPicker.GetInputElement().focus();
		}, Grocy.FormFocusDelay);
	}
}
else
{
	Grocy.Components.ProductPicker.GetPicker().trigger('change');

	if (Grocy.Components.ProductPicker.InProductModifyWorkflow())
	{
		setTimeout(function()
		{
			Grocy.Components.ProductPicker.GetInputElement().focus();
		}, Grocy.FormFocusDelay);
	}
}

if (Grocy.EditMode == "create")
{
	Grocy.RecipePosFormInitialLoadDone = true;
}

$('#display_amount').on('focus', function(e)
{
	if (Grocy.Components.ProductPicker.GetValue().length === 0)
	{
		setTimeout(function()
		{
			Grocy.Components.ProductPicker.GetInputElement().focus();
		}, Grocy.FormFocusDelay);
	}
	else
	{
		$(this).select();
	}
});

$('#recipe-pos-form input').keyup(function(event)
{
	Grocy.FrontendHelpers.ValidateForm('recipe-pos-form');
});

$('#qu_id').change(function(event)
{
	Grocy.FrontendHelpers.ValidateForm('recipe-pos-form');
});

$('#recipe-pos-form input').keydown(function(event)
{
	if (event.keyCode === 13) // Enter
	{
		event.preventDefault();

		if (!Grocy.FrontendHelpers.ValidateForm('recipe-pos-form'))
		{
			return false;
		}
		else
		{
			$('#save-recipe-pos-button').click();
		}
	}
});

$("#only_check_single_unit_in_stock").on("change", function()
{
	if (this.checked)
	{
		$("#display_amount").attr("min", Grocy.DefaultMinAmount);
		Grocy.Components.ProductAmountPicker.AllowAnyQu(true);
		Grocy.FrontendHelpers.ValidateForm("recipe-pos-form");
	}
	else
	{
		$("#display_amount").attr("min", "0");
		Grocy.Components.ProductPicker.GetPicker().trigger("change"); // Selects the default quantity unit of the selected product
		Grocy.Components.ProductAmountPicker.AllowAnyQuEnabled = false;
		Grocy.FrontendHelpers.ValidateForm("recipe-pos-form");
	}
});

if ($("#only_check_single_unit_in_stock").prop("checked"))
{
	Grocy.Components.ProductAmountPicker.AllowAnyQu(true);
}
