﻿import { BoolVal } from '../helpers/extensions';

$("#product_presets_location_id").val(Grocy.UserSettings.product_presets_location_id);
$("#product_presets_product_group_id").val(Grocy.UserSettings.product_presets_product_group_id);
$("#product_presets_qu_id").val(Grocy.UserSettings.product_presets_qu_id);
$("#stock_due_soon_days").val(Grocy.UserSettings.stock_due_soon_days);
$("#stock_default_purchase_amount").val(Grocy.UserSettings.stock_default_purchase_amount);
$("#stock_default_consume_amount").val(Grocy.UserSettings.stock_default_consume_amount);
$("#stock_decimal_places_amounts").val(Grocy.UserSettings.stock_decimal_places_amounts);
$("#stock_decimal_places_prices").val(Grocy.UserSettings.stock_decimal_places_prices);

if (BoolVal(Grocy.UserSettings.show_icon_on_stock_overview_page_when_product_is_on_shopping_list))
{
	$("#show_icon_on_stock_overview_page_when_product_is_on_shopping_list").prop("checked", true);
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
