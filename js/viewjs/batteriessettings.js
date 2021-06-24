function batteriessettingsView(Grocy, scope = null)
{
	var $scope = $;
	if (scope != null)
	{
		$scope = (selector) => $(scope).find(selector);
	}

	Grocy.Use("numberpicker");

	$scope("#batteries_due_soon_days").val(Grocy.UserSettings.batteries_due_soon_days);

	RefreshLocaleNumberInput();

}


window.batteriessettingsView = batteriessettingsView
