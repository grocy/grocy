$('#save-recipe-pos-button').on('click', function(e)
{
	e.preventDefault();

	var jsonData = $('#recipe-pos-form').serializeJSON();
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
				console.error(xhr);
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
		
		Grocy.Api.Get('stock/get-product-details/' + productId,
			function (productDetails)
			{
				$('#amount_qu_unit').text(productDetails.quantity_unit_purchase.name);
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

$('#recipe-pos-form input').keyup(function (event)
{
	Grocy.FrontendHelpers.ValidateForm('recipe-pos-form');
});

$('#recipe-pos-form input').keydown(function (event)
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
