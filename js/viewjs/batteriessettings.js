function batteriessettingsView(Grocy, scope = null)
{
	var $scope = $;
	if (scope != null)
	{
		$scope = (scope) => $(scope).find(scope);
	}

	Grocy.Use("numberpicker");

	$scope("#batteries_due_soon_days").val(Grocy.UserSettings.batteries_due_soon_days);

	RefreshLocaleNumberInput();

}


window.batteriessettingsView = batteriessettingsView
