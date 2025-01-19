if (BoolVal(Grocy.UserSettings.shopping_list_to_stock_workflow_auto_submit_when_prefilled))
{
	$("#shopping_list_to_stock_workflow_auto_submit_when_prefilled").prop("checked", true);
}

if (BoolVal(Grocy.UserSettings.shopping_list_show_calendar))
{
	$("#shopping_list_show_calendar").prop("checked", true);
}

if (BoolVal(Grocy.UserSettings.shopping_list_round_up))
{
	$("#shopping_list_round_up").prop("checked", true);
}

if (BoolVal(Grocy.UserSettings.shopping_list_auto_add_below_min_stock_amount))
{
	$("#shopping_list_auto_add_below_min_stock_amount").prop("checked", true);
}

$("#shopping_list_auto_add_below_min_stock_amount_list_id").val(Grocy.UserSettings.shopping_list_auto_add_below_min_stock_amount_list_id);

$("#shopping_list_auto_add_below_min_stock_amount").on("click", function()
{
	if (this.checked)
	{
		$("#shopping_list_auto_add_below_min_stock_amount_list_id").removeAttr("disabled");
	}
	else
	{
		$("#shopping_list_auto_add_below_min_stock_amount_list_id").attr("disabled", "");
	}
});

RefreshLocaleNumberInput();
