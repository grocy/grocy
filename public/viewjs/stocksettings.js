$("#product_presets_location_id").val(Grocy.UserSettings.product_presets_location_id);
$("#product_presets_product_group_id").val(Grocy.UserSettings.product_presets_product_group_id);
$("#product_presets_qu_id").val(Grocy.UserSettings.product_presets_qu_id);

if (BoolVal(Grocy.UserSettings.shopping_list_to_stock_workflow_auto_submit_when_prefilled))
{
	$("#shopping-list-to-stock-workflow-auto-submit-when-prefilled").prop("checked", true);
}
