$("#chores_due_soon_days").val(Grocy.UserSettings.chores_due_soon_days);

if (BoolVal(Grocy.UserSettings.chores_overview_swap_tracking_buttons))
{
	$("#chores_overview_swap_tracking_buttons").prop("checked", true);
}

RefreshLocaleNumberInput();
