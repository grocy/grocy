if (BoolVal(Grocy.UserSettings.shopping_list_to_stock_workflow_auto_submit_when_prefilled))
{
	$("#shopping-list-to-stock-workflow-auto-submit-when-prefilled").prop("checked", true);
}

if (BoolVal(Grocy.UserSettings.shopping_list_show_calendar))
{
	$("#shopping_list_show_calendar").prop("checked", true);
}

if (BoolVal(Grocy.UserSettings.stock_auto_add_below_min_stock_amount_to_shopping_list))
{
	$("#stock_auto_add_below_min_stock_amount_to_shopping_list").prop("checked", true);
}

$("#stock_auto_add_below_min_stock_amount_to_shopping_list_id").val(Grocy.UserSettings.stock_auto_add_below_min_stock_amount_to_shopping_list_id);

$("#stock_auto_add_below_min_stock_amount_to_shopping_list").on("click", function()
{
	if (this.checked)
	{
		$("#stock_auto_add_below_min_stock_amount_to_shopping_list_id").removeAttr("disabled");
	}
	else
	{
		$("#stock_auto_add_below_min_stock_amount_to_shopping_list_id").attr("disabled", "");
	}
});

RefreshLocaleNumberInput();
