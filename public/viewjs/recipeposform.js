Grocy.RecipePosFormProductChangeCount = 0;

$('#save-recipe-pos-button').on('click', function (e)
{
	e.preventDefault();

	var jsonData = $('#recipe-pos-form').serializeJSON({ checkboxUncheckedValue: "0" });
	jsonData.recipe_id = Grocy.EditObjectParentId;
	delete jsonData.display_amount;

	Grocy.FrontendHelpers.BeginUiBusy("recipe-pos-form");

	if (Grocy.EditMode === 'create')
	{
		Grocy.Api.Post('objects/recipes_pos', jsonData,
			function(result)
			{
				window.parent.postMessage(WindowMessageBag("IngredientsChanged"), Grocy.BaseUrl);
				window.parent.postMessage(WindowMessageBag("CloseAllModals"), Grocy.BaseUrl);
			},
			function(xhr)
			{
				Grocy.FrontendHelpers.EndUiBusy("recipe-pos-form");
				Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response)
			}
		);
	}
	else
	{
		Grocy.Api.Put('objects/recipes_pos/' + Grocy.EditObjectId, jsonData,
			function(result)
			{
				window.parent.postMessage(WindowMessageBag("IngredientsChanged"), Grocy.BaseUrl);
				window.parent.postMessage(WindowMessageBag("CloseAllModals"), Grocy.BaseUrl);
			},
			function(xhr)
			{
				Grocy.FrontendHelpers.EndUiBusy("recipe-pos-form");
				Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response)
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
			function(productDetails)
			{
				Grocy.RecipePosFormProductChangeCount++;
				
				if (Grocy.RecipePosFormProductChangeCount < 3) // This triggers twice on initial page load, however
				{
					Grocy.Components.ProductAmountPicker.Reload(productDetails.product.id, productDetails.quantity_unit_stock.id, true);
				}
				else
				{
					Grocy.Components.ProductAmountPicker.Reload(productDetails.product.id, productDetails.quantity_unit_stock.id);
				}

				if (productDetails.product.allow_partial_units_in_stock == 1)
				{
					$("#display_amount").attr("min", "0.01");
					$("#display_amount").attr("step", "0.01");
					$("#display_amount").parent().find(".invalid-feedback").text(__t('The amount cannot be lower than %s', 0.01.toLocaleString()));
				}
				else
				{
					$("#display_amount").attr("min", "1");
					$("#display_amount").attr("step", "1");
					$("#display_amount").parent().find(".invalid-feedback").text(__t('The amount cannot be lower than %s', '1'));
				}

				$("#not_check_stock_fulfillment").prop("checked", productDetails.product.not_check_stock_fulfillment_for_recipes == 1);

				if (!$("#only_check_single_unit_in_stock").prop("checked"))
				{
					Grocy.Components.ProductAmountPicker.SetQuantityUnit(productDetails.quantity_unit_stock.id);
				}

				$('#display_amount').focus();
				Grocy.FrontendHelpers.ValidateForm('recipe-pos-form');
			},
			function(xhr)
			{
				console.error(xhr);
			}
		);
	}
});

Grocy.FrontendHelpers.ValidateForm('recipe-pos-form');

if (Grocy.Components.ProductPicker.InProductAddWorkflow() === false)
{
	Grocy.Components.ProductPicker.GetInputElement().focus();
}
Grocy.Components.ProductPicker.GetPicker().trigger('change');

$('#display_amount').on('focus', function(e)
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

$('#recipe-pos-form input').keyup(function(event)
{
	Grocy.FrontendHelpers.ValidateForm('recipe-pos-form');
});

$('#recipe-pos-form input').keydown(function(event)
{
	if (event.keyCode === 13) //Enter
	{
		event.preventDefault();

		if (document.getElementById('recipe-pos-form').checkValidity() === false) //There is at least one validation error
		{
			return false;
		}
		else
		{
			$('#save-recipe-pos-button').click();
		}
	}
});

$("#only_check_single_unit_in_stock").on("click", function()
{
	if (this.checked)
	{
		$("#display_amount").attr("min", "0.01");
		$("#display_amount").attr("step", "0.01");
		$("#display_amount").parent().find(".invalid-feedback").text(__t("This cannot be negative"));
		Grocy.Components.ProductAmountPicker.AllowAnyQu(true);
		Grocy.FrontendHelpers.ValidateForm("recipe-pos-form");
	}
	else
	{
		$("#display_amount").attr("min", "0");
		$("#display_amount").attr("step", "1");
		Grocy.Components.ProductPicker.GetPicker().trigger("change"); // Selects the default quantity unit of the selected product
		$("#display_amount").parent().find(".invalid-feedback").text(__t("This cannot be negative and must be an integral number"));
		Grocy.Components.ProductAmountPicker.AllowAnyQuEnabled = false;
		Grocy.FrontendHelpers.ValidateForm("recipe-pos-form");
	}
});

if ($("#only_check_single_unit_in_stock").prop("checked"))
{
	Grocy.Components.ProductAmountPicker.AllowAnyQu(true);
}
