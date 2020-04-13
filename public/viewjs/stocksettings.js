$("#product_presets_location_id").val(Grocy.UserSettings.product_presets_location_id);
$("#product_presets_product_group_id").val(Grocy.UserSettings.product_presets_product_group_id);
$("#product_presets_qu_id").val(Grocy.UserSettings.product_presets_qu_id);
$("#stock_expring_soon_days").val(Grocy.UserSettings.stock_expring_soon_days);
$("#stock_default_purchase_amount").val(Grocy.UserSettings.stock_default_purchase_amount);
$("#stock_default_consume_amount").val(Grocy.UserSettings.stock_default_consume_amount);

if (BoolVal(Grocy.UserSettings.show_icon_on_stock_overview_page_when_product_is_on_shopping_list))
{
	$("#show_icon_on_stock_overview_page_when_product_is_on_shopping_list").prop("checked", true);
}
