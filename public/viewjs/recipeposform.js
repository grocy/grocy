$('#save-recipe-pos-button').on('click', function(e)
{
	e.preventDefault();

	var jsonData = $('#recipe-pos-form').serializeJSON({ checkboxUncheckedValue: "0" });
	jsonData.recipe_id = Grocy.EditObjectParentId;
	console.log(jsonData);
	if (Grocy.EditMode === 'create')
	{
		Grocy.Api.Post('add-object/recipes_pos', jsonData,
			function(result)
			{
				window.location.href = U('/recipe/' + Grocy.EditObjectParentId);
			},
			function(xhr)
			{
				Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response)
			}
		);
	}
	else
	{
		Grocy.Api.Post('edit-object/recipes_pos/' + Grocy.EditObjectId, jsonData,
			function(result)
			{
				window.location.href = U('/recipe/' + Grocy.EditObjectParentId);
			},
			function(xhr)
			{
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
		
		Grocy.Api.Get('stock/get-product-details/' + productId,
			function (productDetails)
			{
				if (!$("#only_check_single_unit_in_stock").is(":checked"))
				{
					$("#qu_id").val(productDetails.quantity_unit_stock.id);
				}
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
		if (document.getElementById('recipe-pos-form').checkValidity() === false) //There is at least one validation error
		{
			event.preventDefault();
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
	}
	else
	{
		$("#qu_id").attr("disabled", "");
		Grocy.Components.ProductPicker.GetPicker().trigger("change");
	}
});
