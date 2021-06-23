import { BoolVal } from '../helpers/extensions';

function stocksettingsView(Grocy, scope = null)
{
	var $scope = $;
	if (scope != null)
	{
		$scope = $(scope).find;
	}


	Grocy.Use("numberpicker");

	$scope("#product_presets_location_id").val(Grocy.UserSettings.product_presets_location_id);
	$scope("#product_presets_product_group_id").val(Grocy.UserSettings.product_presets_product_group_id);
	$scope("#product_presets_qu_id").val(Grocy.UserSettings.product_presets_qu_id);
	$scope("#stock_due_soon_days").val(Grocy.UserSettings.stock_due_soon_days);
	$scope("#stock_default_purchase_amount").val(Grocy.UserSettings.stock_default_purchase_amount);
	$scope("#stock_default_consume_amount").val(Grocy.UserSettings.stock_default_consume_amount);
	$scope("#stock_decimal_places_amounts").val(Grocy.UserSettings.stock_decimal_places_amounts);
	$scope("#stock_decimal_places_prices").val(Grocy.UserSettings.stock_decimal_places_prices);

	if (BoolVal(Grocy.UserSettings.show_icon_on_stock_overview_page_when_product_is_on_shopping_list))
	{
		$scope("#show_icon_on_stock_overview_page_when_product_is_on_shopping_list").prop("checked", true);
	}
	if (BoolVal(Grocy.UserSettings.show_purchased_date_on_purchase))
	{
		$scope("#show_purchased_date_on_purchase").prop("checked", true);
	}

	if (BoolVal(Grocy.UserSettings.show_warning_on_purchase_when_due_date_is_earlier_than_next))
	{
		$scope("#show_warning_on_purchase_when_due_date_is_earlier_than_next").prop("checked", true);
	}

	if (BoolVal(Grocy.UserSettings.stock_default_consume_amount_use_quick_consume_amount))
	{
		$scope("#stock_default_consume_amount_use_quick_consume_amount").prop("checked", true);
		$scope("#stock_default_consume_amount").attr("disabled", "");
	}

	RefreshLocaleNumberInput();

	$scope("#stock_default_consume_amount_use_quick_consume_amount").on("click", function()
	{
		if (this.checked)
		{
			$scope("#stock_default_consume_amount").attr("disabled", "");
		}
		else
		{
			$scope("#stock_default_consume_amount").removeAttr("disabled");
		}
	});

}



window.stocksettingsView = stocksettingsView
