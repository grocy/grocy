if (BoolVal(Grocy.UserSettings.shopping_list_to_stock_workflow_auto_submit_when_prefilled))
{
	$("#shopping-list-to-stock-workflow-auto-submit-when-prefilled").prop("checked", true);
}

if (BoolVal(Grocy.UserSettings.shopping_list_show_calendar))
{
	$("#shopping-list-show-calendar").prop("checked", true);
}

RefreshLocaleNumberInput();
