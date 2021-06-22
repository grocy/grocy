function recipessettingsView(Grocy, scope = null)
{
	var $scope = $;
	if (scope != null)
	{
		$scope = $(scope).find;
	}

	import { BoolVal } from '../helpers/extensions';
	
	if (BoolVal(Grocy.UserSettings.recipe_ingredients_group_by_product_group))
	{
		$("#recipe_ingredients_group_by_product_group").prop("checked", true);
	}
	
	RefreshLocaleNumberInput();
	
}
