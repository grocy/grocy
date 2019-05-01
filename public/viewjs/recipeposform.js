$('#save-recipe-pos-button').on('click', function(e)
{
	e.preventDefault();

	var jsonData = $('#recipe-pos-form').serializeJSON({ checkboxUncheckedValue: "0" });
	jsonData.recipe_id = Grocy.EditObjectParentId;

	Grocy.FrontendHelpers.BeginUiBusy("recipe-pos-form");

	if (Grocy.EditMode === 'create')
	{
		Grocy.Api.Post('objects/recipes_pos', jsonData,
			function(result)
			{
				window.location.href = U('/recipe/' + Grocy.EditObjectParentId);
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
				window.location.href = U('/recipe/' + Grocy.EditObjectParentId);
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
				if (!$("#only_check_single_unit_in_stock").is(":checked"))
				{
					$("#qu_id").val(productDetails.quantity_unit_stock.id);
				}

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

				$("#not_check_stock_fulfillment").prop("checked", productDetails.product.not_check_stock_fulfillment_for_recipes == 1);

				$('#amount').focus();
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
		$("#qu_id").removeAttr("disabled");
		$("#amount").attr("min", "0.01");
		$("#amount").attr("step", "0.01");
		$("#amount").parent().find(".invalid-feedback").text(__t("This cannot be negative"));
		Grocy.FrontendHelpers.ValidateForm("recipe-pos-form");
	}
	else
	{
		$("#qu_id").attr("disabled", "");
		$("#amount").attr("min", "0");
		$("#amount").attr("step", "1");
		Grocy.Components.ProductPicker.GetPicker().trigger("change"); // Selects the default quantity unit of the selected product
		$("#amount").parent().find(".invalid-feedback").text(__t("This cannot be negative and must be an integral number"));
		Grocy.FrontendHelpers.ValidateForm("recipe-pos-form");
	}
});

// Click twice to trigger on-click but not change the actual checked state
$("#only_check_single_unit_in_stock").click();
$("#only_check_single_unit_in_stock").click();
