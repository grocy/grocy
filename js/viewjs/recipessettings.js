import { BoolVal } from '../helpers/extensions';

function recipessettingsView(Grocy, scope = null)
{
	var $scope = $;
	if (scope != null)
	{
		$scope = $(scope).find;
	}

	if (BoolVal(Grocy.UserSettings.recipe_ingredients_group_by_product_group))
	{
		$scope("#recipe_ingredients_group_by_product_group").prop("checked", true);
	}

	RefreshLocaleNumberInput();

}

window.recipessettingsView = recipessettingsView