if (BoolVal(Grocy.UserSettings.recipe_ingredients_group_by_product_group))
{
	$("#recipe_ingredients_group_by_product_group").prop("checked", true);
}

if (BoolVal(Grocy.UserSettings.recipes_show_list_side_by_side))
{
	$("#recipes_show_list_side_by_side").prop("checked", true);
}

if (BoolVal(Grocy.UserSettings.recipes_show_ingredient_checkbox))
{
	$("#recipes_show_ingredient_checkbox").prop("checked", true);
}
