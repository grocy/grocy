import { WindowMessageBag } from '../helpers/messagebag';

function recipeposformView(Grocy, scope = null)
{
	var $scope = $;
	if (scope != null)
	{
		$scope = $(scope).find;
	}

	Grocy.Use("numberpicker");
	Grocy.Use("productamountpicker");
	Grocy.Use("productcard");

	Grocy.RecipePosFormInitialLoadDone = false;

	$scope('#save-recipe-pos-button').on('click', function(e)
	{
		e.preventDefault();

		if ($scope(".combobox-menu-visible").length)
		{
			return;
		}

		var jsonData = $scope('#recipe-pos-form').serializeJSON();
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
		var productId = $scope(e.target).val();

		if (productId)
		{
			Grocy.Components.ProductCard.Refresh(productId);

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
						$scope("#not_check_stock_fulfillment").prop("checked", productDetails.product.not_check_stock_fulfillment_for_recipes == 1);
					}

					if (!$scope("#only_check_single_unit_in_stock").prop("checked") && Grocy.RecipePosFormInitialLoadDone)
					{
						Grocy.Components.ProductAmountPicker.SetQuantityUnit(productDetails.quantity_unit_stock.id);
					}

					$scope('#display_amount').focus();
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

	if (Grocy.Components.ProductPicker.InProductAddWorkflow() === false)
	{
		Grocy.Components.ProductPicker.GetInputElement().focus();
	}
	Grocy.Components.ProductPicker.GetPicker().trigger('change');

	if (Grocy.EditMode == "create")
	{
		Grocy.RecipePosFormInitialLoadDone = true;
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

	$scope('#recipe-pos-form input').keyup(function(event)
	{
		Grocy.FrontendHelpers.ValidateForm('recipe-pos-form');
	});

	$scope('#qu_id').change(function(event)
	{
		Grocy.FrontendHelpers.ValidateForm('recipe-pos-form');
	});

	$scope('#recipe-pos-form input').keydown(function(event)
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
				$scope('#save-recipe-pos-button').click();
			}
		}
	});

	$scope("#only_check_single_unit_in_stock").on("change", function()
	{
		if (this.checked)
		{
			$scope("#display_amount").attr("min", Grocy.DefaultMinAmount);
			Grocy.Components.ProductAmountPicker.AllowAnyQu(true);
			Grocy.FrontendHelpers.ValidateForm("recipe-pos-form");
		}
		else
		{
			$scope("#display_amount").attr("min", "0");
			Grocy.Components.ProductPicker.GetPicker().trigger("change"); // Selects the default quantity unit of the selected product
			Grocy.Components.ProductAmountPicker.AllowAnyQuEnabled = false;
			Grocy.FrontendHelpers.ValidateForm("recipe-pos-form");
		}
	});

	if ($scope("#only_check_single_unit_in_stock").prop("checked"))
	{
		Grocy.Components.ProductAmountPicker.AllowAnyQu(true);
	}

}

window.recipeposformView = recipeposformView