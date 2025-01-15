$("#product_presets_location_id").val(Grocy.UserSettings.product_presets_location_id);
$("#product_presets_product_group_id").val(Grocy.UserSettings.product_presets_product_group_id);
$("#product_presets_qu_id").val(Grocy.UserSettings.product_presets_qu_id);
$("#product_presets_default_due_days").val(Grocy.UserSettings.product_presets_default_due_days);

if (Grocy.FeatureFlags.GROCY_FEATURE_FLAG_STOCK_PRODUCT_OPENED_TRACKING && BoolVal(Grocy.UserSettings.product_presets_treat_opened_as_out_of_stock))
{
	$("#product_presets_treat_opened_as_out_of_stock").prop("checked", true);
}

if (Grocy.FeatureFlags.GROCY_FEATURE_FLAG_LABEL_PRINTER)
{
	$("#product_presets_default_stock_label_type").val(Grocy.UserSettings.product_presets_default_stock_label_type);
}

$("#stock_due_soon_days").val(Grocy.UserSettings.stock_due_soon_days);
$("#stock_default_purchase_amount").val(Grocy.UserSettings.stock_default_purchase_amount);
$("#stock_default_consume_amount").val(Grocy.UserSettings.stock_default_consume_amount);
$("#stock_decimal_places_amounts").val(Grocy.UserSettings.stock_decimal_places_amounts);
$("#stock_decimal_places_prices_input").val(Grocy.UserSettings.stock_decimal_places_prices_input);
$("#stock_decimal_places_prices_display").val(Grocy.UserSettings.stock_decimal_places_prices_display);

if (BoolVal(Grocy.UserSettings.show_icon_on_stock_overview_page_when_product_is_on_shopping_list))
{
	$("#show_icon_on_stock_overview_page_when_product_is_on_shopping_list").prop("checked", true);
}

if (BoolVal(Grocy.UserSettings.stock_overview_show_all_out_of_stock_products))
{
	$("#stock_overview_show_all_out_of_stock_products").prop("checked", true);
}

if (BoolVal(Grocy.UserSettings.show_purchased_date_on_purchase))
{
	$("#show_purchased_date_on_purchase").prop("checked", true);
}

if (BoolVal(Grocy.UserSettings.show_warning_on_purchase_when_due_date_is_earlier_than_next))
{
	$("#show_warning_on_purchase_when_due_date_is_earlier_than_next").prop("checked", true);
}

if (BoolVal(Grocy.UserSettings.stock_default_consume_amount_use_quick_consume_amount))
{
	$("#stock_default_consume_amount_use_quick_consume_amount").prop("checked", true);
	$("#stock_default_consume_amount").attr("disabled", "");
}

if (BoolVal(Grocy.UserSettings.stock_auto_decimal_separator_prices))
{
	$("#stock_auto_decimal_separator_prices").prop("checked", true);
}

RefreshLocaleNumberInput();

$("#stock_default_consume_amount_use_quick_consume_amount").on("click", function()
{
	if (this.checked)
	{
		$("#stock_default_consume_amount").attr("disabled", "");
	}
	else
	{
		$("#stock_default_consume_amount").removeAttr("disabled");
	}
});
